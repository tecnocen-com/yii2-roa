<?php

use Codeception\Util\HttpCode;

class ApiCest
{
    public function versions(ApiTester $I)
    {
        $I->wantTo('Check the container returns the contained api versions.');
        $I->sendGET('/api', []);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'v1' => [],
        ]);
    }
}
