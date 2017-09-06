<?php

namespace tecnocen\roa\test;

trait TesterTrait
{
    /**
     * @var string[] pairs of user_name => oauth2_token for oauth2 auth.
     */
    protected static $tokens = [];

    /**
     * @var string identificator for the auth/logged user.
     */
    protected $loggedUsername;

    /**
     * Saves a token and user by an unique name.
     *
     * @param string $tokenName unique name to index the tokens and models
     * @param string $token oauth2 authorization token
     * @param UserIdentity $user
     */
    public function storeToken($tokenName, $token)
    {
        static::$tokens[$tokenName] = $token;
    }

    /**
     * Authenticates a user stored in `$tokens`
     *
     * @param string $tokenName
     */
    public function amAuthByToken($tokenName)
    {
        $this->amBearerAuthenticated(static::$tokens[$tokenName]);
    }

    /**
     * Checks over the HTTP pagination headers and (optionally) its values.
     */
    public function seePaginationHttpHeaders()
    {
        $this->seeHttpHeaderOnce('X-Pagination-Total-Count');
        $this->seeHttpHeaderOnce('X-Pagination-Page-Count');
        $this->seeHttpHeaderOnce('X-Pagination-Current-Page');
        $this->seeHttpHeaderOnce('X-Pagination-Per-Page');
    }

    /**
     * Checks over the HTTP content type header value.
     */
    public function seeContentTypeHttpHeader(
        $contentType = Tester::HAL_JSON_CONTENT_TYPE
    ) {
        $this->seeHttpHeader('Content-Type', $contentType);
    }
}
