<?php

namespace resources;

use ApiTester;

use app\fixtures\ItemFixture;
use app\fixtures\EmployeeFixture;
use app\fixtures\OauthAccessTokensFixture;
use app\fixtures\ShopFixture;
use app\fixtures\SaleFixture;
use app\fixtures\SaleItemFixture;
use Codeception\Example;
use Codeception\Util\HttpCode;

/**
 * Cest to shop resource.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ShopCest extends \tecnocen\roa\test\AbstractResourceCest
{
    protected function authToken(ApiTester $I)
    {
        $I->amBearerAuthenticated(OauthAccessTokensFixture::SIMPLE_TOKEN);
    }

    /**
     * @depends resources\ProfileCest:fixtures
     */
    public function fixtures(ApiTester $I)
    {
        $I->haveFixtures([
            'item' => ItemFixture::class,
            'shop' => ShopFixture::class,
            'employee' => [
                'class' => EmployeeFixture::class,
                'depends' => [],
            ],
            'sale' => SaleFixture::class,
            'sale_item' => SaleItemFixture::class,
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
        $I->wantTo('Retrieve list of Shop records.');
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
                    'name' => 'Miniso',
                    'expand' => 'employees'
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
     * @depends fixtures
     * @before authToken
     */
    public function view(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve Shop single record.');
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
            'expand employees' => [
                'urlParams' => [
                    'id' => 1,
                    'expand' => 'employees'
                ],
                'httpCode' => HttpCode::OK,
                'response' => [
                    '_embedded' => [
                        'employees' => [
                            ['id' => 1],
                        ],
                    ],
                ],
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
        $I->wantTo('Create a Shop record.');
        $this->internalCreate($I, $example);
    }

    /**
     * @return array<string,array<string,array<string,string>>> data for test `create()`.
     */
    protected function createDataProvider()
    {
        return [
            'create shop 4' => [
                'data' => ['name' => 'Shop 4'],
                'httpCode' => HttpCode::CREATED,
            ],
            'unique' => [
                'data' => ['name' => 'Shop 4'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' => 'Shop Name "Shop 4" has already been taken.'
                ],
            ],
            'to short' => [
                'data' => ['name' => 'Shop'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' => 'Shop Name should contain at least 6 characters.'
                ],
            ],
            'not blank' => [
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' => 'Shop Name cannot be blank.'
                ],
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
        $I->wantTo('Update a Shop record.');
        $this->internalUpdate($I, $example);
    }

    /**
     * @return array[] data for test `update()`.
     */
    protected function updateDataProvider()
    {
        return [
            'update shop 1' => [
                'urlParams' => ['id' => '1'],
                'data' => ['name' => 'Shop 1'],
                'httpCode' => HttpCode::OK,
            ],
            'to short' => [
                'urlParams' => ['id' => '1'],
                'data' => ['name' => 'Shop'],
                'httpCode' => HttpCode::UNPROCESSABLE_ENTITY,
                'validationErrors' => [
                    'name' => 'Shop Name should contain at least 6 characters.'
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
        $I->wantTo('Delete a Shop record.');
        $this->internalDelete($I, $example);
    }


    /**
     * @return array[] data for test `delete()`.
     */
    protected function deleteDataProvider()
    {
        return [
            'delete shop 1' => [
                'urlParams' => ['id' => 4],
                'httpCode' => HttpCode::NO_CONTENT,
            ],
            'not found' => [
                'urlParams' => ['id' => 4],
                'httpCode' => HttpCode::NOT_FOUND,
                'validationErrors' => [
                    'name' => 'The record "4" does not exists.'
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
        return 'v1/shop';
    }
}
