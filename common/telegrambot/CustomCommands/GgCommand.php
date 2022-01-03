<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

class GgCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'gg';
    /**
     * @var string
     */
    protected $description = 'End game';
    /**
     * @var string
     */
    protected $usage = '/gg';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        /** @var Game $game */
        $game = Yii::$app->game;

        $session = $game->instanceSession($this->getMessage()->getChat()->getId());
        $text = 'Game over, results: ' . PHP_EOL;
        try {
            $gameResults = $session->getTransactionsSummary();
            foreach ($gameResults as $player => $sum) {
                $text .= "{$player} $sum" . PHP_EOL;
            }
        } catch (\Throwable $exception) {
            $text .= ' (error)';
        }
        $session->end();

        return $this->replyToChat(
            $text, ['reply_markup' => Keyboard::remove()]
        );
    }
}
