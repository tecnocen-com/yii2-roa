<?php

use Codeception\Example;
use Codeception\Util\HttpCode;
use app\fixtures\OauthAccessTokensFixture;

/**
 * Cest to shop resource.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ProfileCest extends \tecnocen\roa\test\AbstractResourceCest
{
    protected function authToken(ApiTester $I)
    {
        $I->amBearerAuthenticated(OauthAccessTokensFixture::SIMPLE_TOKEN);
    }

    /**
     * @param  ApiTester $I
     * @before authToken
     */
    public function view(ApiTester $I)
    {
        $I->wantTo('Retrieve Profile record.');
        $I->sendGET($this->getRoutePattern());
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'id' => 'integer:>0',
            'username' => 'string'
        ]);
        $I->dontSeeRecordJsonType($I);

    }

    /**
     * @param  ApiTester $I
     * @before authToken
     */
    public function create(ApiTester $I)
    {
        $I->wantTo('Create a Profile record.');
        $I->sendPOST($this->getRoutePattern(), [
            'username' => 'erau2'
        ]);
        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
    }

    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider updateDataProvider
     * @before authToken
     */
    public function update(ApiTester $I, Example $example)
    {
        $I->wantTo('Update a Profile record.');
        $this->internalUpdate($I, $example);
        $this->dontSeeRecordJsonType($I);
    }
    /**
     * @return array[] data for test `update()`.
     */
    protected function updateDataProvider()
    {
        return [
            'update username' => [
                'data' => ['username' => 'erau2'],
                'httpCode' => HttpCode::OK,
            ],
            'to short' => [
                'data' => ['username' => 'er'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'username' => 'Username should contain at least 4 characters.'
                ],
            ],
            'empty username' => [
                'data' => ['username' => ''],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'username' => 'Username is required.'
                ],
            ],
        ];
    }

    /**
     * @param  ApiTester $I
     * @before authToken
     */
    public function delete(ApiTester $I)
    {
        $I->wantTo('Delete a Profile record.');
        $I->sendDELETE($this->getRoutePattern(). '/1');
        $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
    }

    /**
     * @return array[] dontSeeResponseMatchesJsonType() for test.
     */    
    protected function dontSeeRecordJsonType(ApiTester $I){
        return $I->dontSeeResponseMatchesJsonType([
            'auth_key' => 'string',
            'password_hash' => 'string',
            'password_reset_token' => 'string',
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function recordJsonType()
    {
        return [
            'id' => 'integer:>0',
            'name' => 'string',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getRoutePattern()
    {
        return '/v1/profile';
    }
}
