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

    public function fixtures(ApiTester $I)
    {
        $I->haveFixtures([
            'access_tokens' => OauthAccessTokensFixture::class
        ]);
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider indexDataProvider
     * @depends fixtures
     * @before authToken
     */
    public function index(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve Profile');
        $this->internalIndex($I, $example);
        $this->dontSeeRecordJsonType();
    }

    /**
     * @return array<string,array> for test `index()`.
     */
    protected function indexDataProvider()
    {
        return [
            'profile' => [
                'httpCode' => HttpCode::OK,
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider viewDataProvider
     * @depends fixtures
     * @before authToken
     */
    public function view(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve Profile single record.');
        $this->internalView($I, $example);
        if (isset($example['response'])) {
            $I->seeResponseContainsJson($example['response']);
        }
    }
    /**
     * @return array[] data for test `view()`.
     */
    protected function viewDataProvider()
    {
        return [
            'find profile by id' => [
                'urlParams' => [
                    'id' => 1,
                ],
                'httpCode' => HttpCode::METHOD_NOT_ALLOWED,
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider createDataProvider
     * @depends fixtures
     * @before authToken
     */
    public function create(ApiTester $I, Example $example)
    {
        $I->wantTo('Create a Profile record.');
        $this->internalCreate($I, $example);
    }
    /**
     * @return array<string,array<string,array<string,string>>> data for test `create()`.
     */
    protected function createDataProvider()
    {
        return [
            'create profile' => [
                'data' => ['username' => 'erau 2'],
                'httpCode' => HttpCode::METHOD_NOT_ALLOWED,
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider updateDataProvider
     * @depends fixtures
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
     * @param  Example $example
     * @dataprovider deleteDataProvider
     * @depends fixtures
     * @before authToken
     */
    public function delete(ApiTester $I, Example $example)
    {
        $I->wantTo('Delete a Profile record.');
        $this->internalDelete($I, $example);
    }
    /**
     * @return array[] data for test `delete()`.
     */
    protected function deleteDataProvider()
    {
        return [
            'delete profile 1' => [
                'urlParams' => ['id' => 4],
                'httpCode' => HttpCode::METHOD_NOT_ALLOWED,
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    protected function recordJsonType()
    {
        return [
            'id' => 'integer:>0',
            'username' => 'string',
        ];
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
    protected function getRoutePattern()
    {
        return 'v1/profile';
    }
}
