<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'telegram' => [
            'class' => \evgeralt\yii2telegram\TelegramBot::class,
            'apiKey' => getenv('TELEGRAM_API_KEY'),
            'botName' => getenv('TELEGRAM_BOT_NAME'),
        ]
    ],
];
