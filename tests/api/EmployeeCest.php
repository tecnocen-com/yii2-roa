<?php

use Codeception\Example;
use Codeception\Util\HttpCode;
use app\fixtures\OauthAccessTokensFixture;
use app\fixtures\EmployeeFixture;

/**
 * Cest to Employee resource.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class EmployeeCest extends \tecnocen\roa\test\AbstractResourceCest
{
    protected function authToken(ApiTester $I)
    {
        $I->amBearerAuthenticated(OauthAccessTokensFixture::SIMPLE_TOKEN);
    }

    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider indexDataProvider
     * @depends ShopCest:fixtures
     * @before authToken
     */
    public function index(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve list of Employee records.');
        $this->internalIndex($I, $example);
    }

    /**
     * @return array<string,array> for test `index()`.
     */
    protected function indexDataProvider()
    {
        return [
            'list' => [
                'urlParams' => [
                    'shop_id' => 2,
                    'expand' => 'shop'
                ],
                'httpCode' => HttpCode::OK,
                'headers' => [
                    'X-Pagination-Total-Count' => 2,
                ],
            ],
            'not found shop' => [
                'urlParams' => [
                    'shop_id' => 10
                ],
                'httpCode' => HttpCode::NOT_FOUND,
            ],
            'filter by name' => [
                'urlParams' => [
                    'shop_id' => 2,
                    'name' => 'Robert',
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
     * @depends ShopCest:fixtures
     * @before authToken
     */
    public function view(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve Employee single record.');
        $this->internalView($I, $example);
    }
    /**
     * @return array<string,array<string,string>> data for test `view()`.
     */
    protected function viewDataProvider()
    {
        return [
            'single record' => [
                'urlParams' => [
                    'shop_id' => 2,
                    'id' => 3,
                    'expand' => 'shop'
                ],
                'httpCode' => HttpCode::OK,
                'response' => [
                    '_embedded' => [
                        'shop' => [
                            ['id' => 2],
                        ],
                    ],
                ],
            ],
            'not found employee record' => [
                'url' => '/v1/shop/1/employee/3',
                'httpCode' => HttpCode::NOT_FOUND,
            ],
            'not found shop record' => [
                'url' => '/v1/shop/10/employee/10',
                'httpCode' => HttpCode::NOT_FOUND,
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider createDataProvider
     * @depends ShopCest:fixtures
     * @before authToken
     */
    public function create(ApiTester $I, Example $example)
    {
        $I->wantTo('Create a Employee record.');
        $this->internalCreate($I, $example);
    }
    /**
     * @return array<string,array<string,string|array<string,string>>> data for test `create()`.
     */
    protected function createDataProvider()
    {
        return [
            'create employee 3' => [
                'urlParams' => [
                    'shop_id' => 1
                ],
                'data' => ['name' => 'Employee 3'],
                'httpCode' => HttpCode::CREATED,
            ],
            'unique' => [
                'urlParams' => [
                    'shop_id' => 1
                ],
                'data' => ['name' => 'Employee 3'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' =>
                        'Employee Name "Employee 3" has already been taken.'
                ],
            ],
            'to short' => [
                'urlParams' => [
                    'shop_id' => 1
                ],
                'data' => ['name' => 'wo'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' =>
                        'Employee Name should contain at least 6 characters.'
                ],
            ],
            'not blank' => [
                'urlParams' => [
                    'shop_id' => 1
                ],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' => 'Employee Name cannot be blank.'
                ],
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider updateDataProvider
     * @depends ShopCest:fixtures
     * @before authToken
     */
    public function update(ApiTester $I, Example $example)
    {
        $I->wantTo('Update a Employee record.');
        $this->internalUpdate($I, $example);
    }
    /**
     * @return array[] data for test `update()`.
     */
    protected function updateDataProvider()
    {
        return [
            'update employee 1' => [
                'url' => '/v1/shop/1/employee/1',
                'data' => ['name' => 'employee 7'],
                'httpCode' => HttpCode::OK,
            ],
            'to short' => [
                'url' => '/v1/shop/1/employee/1',
                'data' => ['name' => 'em'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' => 'Employee Name should contain at least 6 characters.'
                ],
            ],
        ];
    }
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider deleteDataProvider
     * @depends ShopCest:fixtures
     * @before authToken
     */
    public function delete(ApiTester $I, Example $example)
    {
        $I->wantTo('Delete a Employee record.');
        $this->internalDelete($I, $example);
    }
    /**
     * @return array[] data for test `delete()`.
     */
    protected function deleteDataProvider()
    {
        return [
            'shop not found' => [
                'url' => '/v1/shop/10/employee/1',
                'httpCode' => HttpCode::NOT_FOUND,
            ],
            'delete Employee 7' => [
                'url' => '/v1/shop/1/employee/7',
                'httpCode' => HttpCode::NO_CONTENT,
            ],
            'not found' => [
                'url' => '/v1/shop/1/employee/7',
                'httpCode' => HttpCode::NOT_FOUND,
                'validationErrors' => [
                    'name' => 'The record "8" does not exists.'
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
            'name' => 'string',
        ];
    }
    /**
     * @inheritdoc
     */
    protected function getRoutePattern()
    {
        return 'v1/shop/<shop_id:\d+>/employee';
    }
}
