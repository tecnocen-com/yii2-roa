<?php

namespace tecnocen\roa\test;

use yii\web\UserIdentity;

/**
 * Interface to test ROA resources services.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
interface Tester
{
    const HAL_JSON_CONTENT_TYPE = 'application/hal+json; charset=UTF-8';

    const HAL_XML_CONTENT_TYPE = 'application/hal+xml; charset=UTF-8';

    /**
     * Saves a token identified by an unique name.
     *
     * @param string $tokenName unique name to identify the tokens.
     * @param string $token oauth2 authorization token
     */
    public function storeToken($tokenName, $token);

    /**
     * Authenticates a user stored in `$tokens`
     *
     * @param string $tokenName
     */
    public function amAuthByToken($tokenName);

    /**
     * Checks over the HTTP pagination headers and (optionally) its values.
     */
    public function seePaginationHttpHeaders();

    /**
     * Checks over the HTTP content type header value.
     */
    public function seeContentTypeHttpHeader(
        $contentType = self::HAL_CONTENT_TYPE
    );
}
