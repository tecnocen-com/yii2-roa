<?php

use tecnocen\roa\controllers\ProfileResource;
use tecnocen\roa\modules\ApiVersion;
use tecnocen\roa\urlRules\SingleRecord;

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
                        'class' => ApiVersion::class,
                        'resources' => [
                            'profile' => [
                                'class' => ProfileResource::class,
                                'urlRule' => ['class' => SingleRecord::class],
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
