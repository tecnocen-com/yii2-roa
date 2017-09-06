<?php

namespace tecnocen\roa\test;

use Codeception\Example;
use Codeception\Util\HttpCode;
use tecnocen\roa\urlRules\Resource as ResourceUrlRule;
use yii\web\UrlManager;

abstract class AbstractResourceCest
{
    /**
     * @var UrlManager url manager used to parse Route's for the services.
     */
    protected $urlManager;

    /**
     * Initializes the `$urlManager` object
     */
    public function __construct()
    {
        $this->urlManager = new UrlManager([
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'cache' => false,
            'rules' => [
                $this->getRoutePattern() . '/<id:\d+>'=> 'test/action',
                $this->getRoutePattern() => 'test/action',
            ],
        ]);
    }

    /**
     * Authenticates a user identified by 'authUser' index in the `$example`.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function authUser(Tester $I, Example $example)
    {
        if (!empty($example['tokenName'])) {
            $I->amAuthByToken($example['tokenName']);
        }
    }

    /**
     * Parses the Route for the test using `$urlManager` and `getRoutePattern()`
     *
     * @param Example $example
     * @return string the url which will be used for the service.
     */
    protected function parseUrl(Example $example)
    {
        if (isset($example['url'])) {
            return $example['url'];
        }
        $params = isset($example['urlParams']) ? $example['urlParams'] : [];
        $params[0] = 'test/action';

        return $this->urlManager->createUrl($params);
    }

    /**
     * Handles the internal logic when running a test on a collection resource.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function internalIndex(Tester $I, Example $example)
    {
        // Authenticates configured user.
        $this->authUser($I, $example);

        // Send request
        $I->sendGET($this->parseUrl($example));

        // Checks the response has the required headers and body.
        $this->checkResponse([
            HttpCode::OK => 'checkSuccessIndexResponse',
            HttpCode::UNPROCESSABLE_ENTITY => 'checkValidationResponse',
        ], $I, $example);
    }

    /**
     * Handles the internal logic when running a test on a creating a record.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function internalCreate(Tester $I, Example $example)
    {
        // Authenticates configured user.
        $this->authUser($I, $example);

        // Send request
        $I->sendPOST(
            $this->parseUrl($example),
            isset($example['data']) ? $example['data'] : []
        );

        // Checks the response has the required headers and body.
        $this->checkResponse([
            HttpCode::CREATED => 'checkSuccessCreateResponse',
            HttpCode::UNPROCESSABLE_ENTITY => 'checkValidationResponse',
        ], $I, $example);
    }

    /**
     * Handles the internal logic when running a test on a record resource.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function internalView(Tester $I, Example $example)
    {
        // Authenticates configured user.
        $this->authUser($I, $example);

        // Send request
        $I->sendGET($this->parseUrl($example));

        // Checks the response has the required headers and body.
        $this->checkResponse([
            HttpCode::OK => 'checkSuccessViewResponse',
        ], $I, $example);
    }

    /**
     * Checks the response Http code and the response based on the Http code and
     * a list of pairs 'httpCode' => 'responseMethod' provided in the
     * `$responses` parameter.
     *
     * If the Http Code in the response doesn't match any of the keys in
     * `$responses` then `checkErrorResponse` will be used instead.
     *
     * @param array $responses pairs of 'httpCode' => 'responseMethod' which
     * will determine how to check the response.
     * @param Tester $I
     * @param Example $example
     */
    protected function checkResponse(
        array $responses,
        Tester $I,
        Example $example
    ) {
        $I->seeResponseCodeIs($example['httpCode']);
        if (isset($responses[$example['httpCode']])) {
            $responseMethod = $responses[$example['httpCode']];
            $this->$responseMethod($I, $example);
        } else {
            $this->checkErrorResponse($I, $example);
        }
    }

    /**
     * Checks an expected success collection response.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function checkSuccessIndexResponse(Tester $I, Example $example)
    {
        $I->seeResponseMatchesJsonType($this->recordJsonType());
        $I->seeContentTypeHttpHeader();
        $I->seePaginationHttpHeaders();
    }

    /**
     * Checks an expected success record response.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function checkSuccessViewResponse(Tester $I, Example $example)
    {
        $I->seeResponseMatchesJsonType($this->recordJsonType());
        $I->seeContentTypeHttpHeader();
    }

    /**
     * Checks an expected success record creation response.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function checkSuccessCreateResponse(Tester $I, Example $example)
    {
        $this->checkSuccessViewResponse($I, $example);
        $I->seeHttpHeaderOnce('Location');
        $I->seeResponseContainsJson(['_links' => [
            'self' => $I->grabHttpHeader('Location'),
        ]]);
    }

    /**
     * Checks an expected response containing validation errors.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function checkValidationResponse(Tester $I, Example $example)
    {
        $I->seeResponseMatchesJsonType([[
            'field' => 'string',
            'message' => 'string',
        ]]);
        if (empty($example['validationErrors'])) {
            return;
        }
        foreach ($example['validationErrors'] as $field => $message) {
            $I->seeResponseContainsJson([
                'field' => $field,
                'message' => $message,
            ]);
        }
    }

    /**
     * Checks an expected error response from user input.
     *
     * @param Tester $I
     * @param Example $example
     */
    protected function checkErrorResponse(Tester $I, Example $example)
    {
        $I->seeResponseMatchesJsonType([
            'name' => 'string',
            'message' => 'string',
            'code' => 'integer',
        ]);
        if (isset($example['error'])) {
            $I->seeResponseContainsJson($example['error']);
        }
    }

    /**
     * Expected Json Type for each resource.
     *
     * @return array
     * @see http://codeception.com/docs/modules/REST#seeResponseMatchesJsonType
     */
    abstract protected function recordJsonType();

    /**
     * @return string route pattern to create the
     */
    abstract protected function getRoutePattern();
}
