<?php

use Codeception\Util\HttpCode;

class V1Cest
{
    /**
     * @depends ApiCest:versions
     */
    public function routes(ApiTester $I)
    {
        $I->wantTo('Check the v1 version has routes.');
        $I->sendGET('/v1', []);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->haveHttpHeader('Content-Type', 'application/hal+json');
        $I->seeResponseContainsJson([
            'routes' => ['/', 'profile'],
        ]);
        $I->seeResponseMatchesJsonType([
            '_links' => [
                'self' => 'string:url',
                'apidoc' => 'string:url',
            ],
        ]);
    }
}
