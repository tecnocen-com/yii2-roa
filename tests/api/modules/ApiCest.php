<?php

namespace modules;

use ApiTester;
use Codeception\Util\HttpCode;

class ApiCest
{
    public function versions(ApiTester $I)
    {
        $I->wantTo('Check the container returns the contained api versions.');
        $I->sendGET('/', []);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'v1' => [],
        ]);
    }
}
