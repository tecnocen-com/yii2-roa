<?php

namespace resources;

use ApiTester;
use app\fixtures\OauthAccessTokensFixture;
use Codeception\Example;
use Codeception\Util\HttpCode;

/**
 * Cest to shop resource.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ShopRestoreCest extends \tecnocen\roa\test\AbstractResourceCest
{
    protected function authToken(ApiTester $I)
    {
        $I->amBearerAuthenticated(OauthAccessTokensFixture::SIMPLE_TOKEN);
    }

    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider restoreDataProvider
     * @before authToken
     */
    public function restore(ApiTester $I, Example $example)
    {
        $I->wantTo('Restore a Shop record.');
        $this->internalUpdate($I, $example);
    }

    /**
     * @return array[] data for test `delete()`.
     */
    protected function restoreDataProvider()
    {
        return [
            'recover shop 4' => [
                'urlParams' => ['id' => 4],
                'httpCode' => HttpCode::OK,
                'data' => [],
            ],
            'not found' => [
                'urlParams' => ['id' => 4],
                'httpCode' => HttpCode::NOT_FOUND,
                'data' => [],
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
        return 'v1/shop-restore';
    }
}
