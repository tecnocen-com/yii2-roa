<?php

use tecnocen\roa\modules\ApiVersion;
use tecnocen\roa\controllers\ProfileResource;

return [
    'id' => 'yii2-roa-tests',
    'basePath' => dirname(__DIR__),
    'language' => 'en-US',
    'aliases' => [
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
        '@bower' => VENDOR_DIR . '/bower',
    ],
    'bootstrap' => ['api'],
    'modules' => [
        'api' => [
            'class' => tecnocen\roa\modules\ApiContainer::class,
            'versions' => [
                'v1' => [
                    'class' => ApiVersion::class,
                    'resources' => [
                        'profile' => [
                            'class' => ProfileResource::class,
                        ],
                    ],
                ],
                'dev' => [
                    'class' => ApiVersion::class,
                ],
                'stable' => [
                    'class' => ApiVersion::class,
                ],
                'deprecated' => [
                    'class' => ApiVersion::class,
                ],
                'obsolete' => [
                    'class' => ApiVersion::class,
                ],
            ],
        ],
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
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
];
