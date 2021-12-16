<?php

namespace console\controllers;

use evgeralt\yii2telegram\TelegramBot;
use Yii;

class GetUpdatesController extends \yii\console\Controller
{
    public function actionRun()
    {
        /** @var TelegramBot $bot */
        $bot = Yii::$app->telegram;


        var_dump($bot->client->getApiKey());
    }
}
