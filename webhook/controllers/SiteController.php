<?php

namespace webhook\controllers;

use evgeralt\yii2telegram\TelegramBot;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionSetHook()
    {
        /** @var TelegramBot $telegram */
        $telegram = Yii::$app->telegram;
        $telegram->client->setWebhook(Yii::$app->params['webhookUrl']);

        return 'ok';
    }

    public function actionHook($id)
    {
        if ($id !== Yii::$app->params['webhookSecretKey']) {
            return new BadRequestHttpException();
        }

        /** @var TelegramBot $telegram */
        $telegram = Yii::$app->telegram;
        $telegram->client->handle();

        return '';
    }

    public function actionError()
    {
        return 'err';
    }
}
