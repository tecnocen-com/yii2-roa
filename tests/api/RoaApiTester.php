<?php
namespace tests\api;

use Codeception\Actor;
use tecnocen\roa\test\Tester as RoaTester;
use tecnocen\roa\test\TesterTrait as RoaTesterTrait;
use tecnocen\roa\test\AbstractResourceCest;
use tecnocen\roa\test\AbstractAccessTokenCest;

class RoaApiTester extends Actor implements RoaTester
{
    use RoaTesterTrait;
}

?>