<?php

namespace console\controllers;

use evgeralt\yii2telegram\TelegramBot;
use Yii;

class GetUpdatesController extends \yii\console\Controller
{
    public function actionRun()
    {
        /** @var TelegramBot $telegram */
        $telegram = Yii::$app->telegram;

        $serverResponse = $telegram->client->handleGetUpdates();
        if ($serverResponse->isOk()) {
            $updateCount = count($serverResponse->getResult());
            echo date('Y-m-d H:i:s') . ' - Processed ' . $updateCount . ' updates' . PHP_EOL;
        } else {
            echo date('Y-m-d H:i:s') . ' - Failed to fetch updates' . PHP_EOL;
            echo $serverResponse->printError();
        }
    }
}
