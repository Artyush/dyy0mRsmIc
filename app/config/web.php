<?php

use app\services\StoryService;
use app\services\MailService;
use app\services\StoryRateLimiter;
use app\repositories\StoryRepository;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'StoryValut',
    'defaultRoute' => 'story/index',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '3LJO_XHaUHlVm4A0mvK6KUiyYZlAawAQ',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => false,
            'transport' => [
                'dsn' => 'smtp://mailhog:1025',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'story/index',
                'story' => 'story/index',
                'story/create' => 'story/create',
                'story/captcha' => 'story/captcha',
                'story/edit/<id:\d+>/<token:[A-Fa-f0-9]{32}>' => 'story/edit',
                'story/confirm-delete/<id:\d+>/<token:[A-Fa-f0-9]{32}>' => 'story/confirm-delete',
                'story/delete/<id:\d+>/<token:[A-Fa-f0-9]{32}>' => 'story/delete',
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

$container = Yii::$container;
$container->set(StoryRepository::class, StoryRepository::class);
$container->set(StoryRateLimiter::class, StoryRateLimiter::class);
$container->set(MailService::class, MailService::class);
$container->set(StoryService::class, function() use ($container) {
    return new StoryService(
        $container->get(StoryRateLimiter::class),
        $container->get(MailService::class),
        $container->get(StoryRepository::class),
    );
});
$container->set(\yii\mail\MailerInterface::class, \yii\symfonymailer\Mailer::class);

return $config;
