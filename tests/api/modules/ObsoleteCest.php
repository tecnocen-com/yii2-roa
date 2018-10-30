<?php

namespace modules;

use ApiTester;
use Codeception\Util\HttpCode;

class ObsoleteCest
{
    /**
     * @depends modules\ApiCest:versions
     */
    public function routes(ApiTester $I)
    {
        $I->wantTo('Check the Obsolete version has routes.');
        $I->sendGET('/obsolete', []);
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
