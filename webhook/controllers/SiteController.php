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
        $telegram->client->setWebhook(getenv('TELEGRAM_WEBHOOK_URL'));

        return 'ok';
    }

    public function actionHook($id)
    {
        if ($id !== getenv('WEBHOOK_SECRET_KEY')) {
            return new BadRequestHttpException();
        }

        /** @var TelegramBot $telegram */
        $telegram = Yii::$app->telegram;
        $telegram->client->handle();

        return '';
    }

    public function actionError()
    {
        return $this->redirect(['set-hook']);
    }
}
