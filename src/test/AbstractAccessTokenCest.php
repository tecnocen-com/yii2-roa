<?php

namespace tecnocen\roa\test;

use Codeception\{Example, Util\HttpCode};

abstract class AbstractAccessTokenCest
{
    /**
     * Generates and stores an auth token.
     *
     * @param Tester $I
     * @param Example $example must contain keys:
     * - client: string http client.
     * - clientPass: string http password.
     * - user: string system user.
     * - userPass: string user password.
     * - tokenName: token name used to store the auth token.
     */
    protected function generateToken(Tester $I, Example $example)
    {
        $I->amHttpAuthenticated($example['client'], $example['clientPass']);
        $I->sendPOST($this->getRoute(), [
            'grant_type' => 'password',
            'username' => $example['user'],
            'password' => $example['userPass'],
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'access_token' => 'string:regex(/[0-9a-f]{40}/)',
            'refresh_token' => 'string:regex(/[0-9a-f]{40}/)',
        ]);
        $I->storeToken(
            $example['tokenName'],
            $I->grabDataFromResponseByJsonPath('access_token')[0]
        );
    }

    /**
     * @return string url route which generates the token.
     */
    protected function getRoute()
    {
        return 'oauth2/token';
    }
}
