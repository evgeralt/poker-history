<?php

namespace webhook\controllers;

use evgeralt\yii2telegram\TelegramBot;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        /** @var TelegramBot $telegram */
        $telegram = Yii::$app->telegram;
        $telegram->client->setWebhook(Yii::$app->params['webhookUrl']);

        return 'ok';
    }

    public function actionError()
    {
        return 'error';
    }
}
