<?php

namespace app\api\modules;

use tecnocen\roa\controllers\ProfileResource;
use tecnocen\roa\urlRules\SingleRecord;

class Obsolete extends \tecnocen\roa\modules\ApiVersion
{

    public $releaseDate = '2010-06-15';
    public $deprecationDate = '2016-01-01';
    public $obsoleteDate = '2017-12-31';

    /**
     * @inheritdoc
     */
    public $resources = [
        'profile' => [
            'class' => ProfileResource::class,
            'urlRule' => ['class' => SingleRecord::class],
        ],
    ];

    public $apidoc = 'http://mockapi.com/v1';
}
