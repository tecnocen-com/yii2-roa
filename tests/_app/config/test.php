<?php

use tecnocen\roa\controllers\ProfileResource;
use tecnocen\roa\urlRules\SingleRecord;
use app\api\modules\Version;

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common.php',
    [
        'id' => 'yii2-roa-tests',
        'bootstrap' => ['api'],
        'modules' => [
            'api' => [
                'class' => tecnocen\roa\modules\ApiContainer::class,
                'versions' => [
                    'v1' => [
                        'class' => Version::class,
                    ],
                    'dev' => [
                        'class' => Version::class,
                    ],
                    'stable' => [
                        'class' => Version::class,
                    ],
                    'deprecated' => [
                        'class' => Version::class,
                    ],
                    'obsolete' => [
                        'class' => Version::class,
                    ],
                ],
            ],
        ],
        'components' => [
            'mailer' => [
                'useFileTransport' => true,
            ],
            'user' => ['identityClass' => app\models\User::class],
            'urlManager' => [
                'showScriptName' => true,
                'enablePrettyUrl' => true,
            ],
            'request' => [
                'cookieValidationKey' => 'test',
                'enableCsrfValidation' => false,
            ],
        ],
        'params' => [],
    ]
);
