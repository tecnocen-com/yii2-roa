<?php

use Codeception\Example;
use Codeception\Util\HttpCode;
use app\fixtures\FileFixture;
use app\fixtures\OauthAccessTokensFixture;

/**
 * Cest to shop resource.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class FileCest extends \tecnocen\roa\test\AbstractResourceCest
{
    protected function authToken(ApiTester $I)
    {
        $I->amBearerAuthenticated(OauthAccessTokensFixture::SIMPLE_TOKEN);
    }

    /**
     * @depends ProfileCest:fixtures
     */
    public function fixtures(ApiTester $I)
    {
        $I->haveFixtures([
            'file' => FileFixture::class,
        ]);
    }

    /**
     * @param  ApiTester $I
     * @depends fixtures
     * @before authToken
     */
    public function create(ApiTester $I)
    {
        $I->wantTo('Create a File record.');
        $I->sendPOST($this->getRoutePattern(), null, [
            'path' => [
                'name' => 'sample.jpeg',
                'type' => 'image/jpeg',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir('sample.jpeg')),
                'tmp_name' => codecept_data_dir('sample.jpeg')
            ]
        ]);
        $I->seeResponseCodeIs(HttpCode::CREATED);
    }

    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider indexDataProvider
     * @before authToken
     */
    public function index(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve list of File records.');
        $this->internalIndex($I, $example);
    }

    /**
     * @return array<string,array> for test `index()`.
     */
    protected function indexDataProvider()
    {
        return [
            'list' => [
                'httpCode' => HttpCode::OK,
            ],
            'filter by name' => [
                'urlParams' => [
                    'mime_type' => 'jpeg',
                ],
                'httpCode' => HttpCode::OK,
                'headers' => [
                    'X-Pagination-Total-Count' => 1,
                ],
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider viewDataProvider
     * @before authToken
     */
    public function view(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve File single record.');
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
            'file 1' => [
                'urlParams' => [
                    'id' => 1,
                ],
                'httpCode' => HttpCode::OK,
            ],
        ];
    }

    /**
     * @param  ApiTester $I
     * @before authToken
     */
    public function update(ApiTester $I)
    {
        $I->wantTo('Update a File record.');
        $I->sendPOST($this->getRoutePattern().'/1', null, [
            'path' => [
                'name' => 'sample.jpeg',
                'type' => 'image/jpeg',
                'error' => UPLOAD_ERR_OK,
                'size' => filesize(codecept_data_dir('sample.jpeg')),
                'tmp_name' => codecept_data_dir('sample.jpeg')
            ]
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider deleteDataProvider
     * @before authToken
     */
    public function delete(ApiTester $I, Example $example)
    {
        $I->wantTo('Delete a File record.');
        $this->internalDelete($I, $example);
    }
    /**
     * @return array[] data for test `delete()`.
     */
    protected function deleteDataProvider()
    {
        return [
            'delete file 1' => [
                'urlParams' => ['id' => 1],
                'httpCode' => HttpCode::NO_CONTENT,
            ],
            'not found' => [
                'urlParams' => ['id' => 1],
                'httpCode' => HttpCode::NOT_FOUND,
                'validationErrors' => [
                    'name' => 'The record "1" does not exists.'
                ],
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
        ];
    }
    /**
     * @inheritdoc
     */
    protected function getRoutePattern()
    {
        return '/v1/file';
    }
}
