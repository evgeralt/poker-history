<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

class InfoCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'info';
    /**
     * @var string
     */
    protected $description = 'Info';
    /**
     * @var string
     */
    protected $usage = '/info';

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
        $text = 'Bank info:' . PHP_EOL;
        foreach ($session->getPlayers() as $player) {
            $text .= "{$player->user->getFullName()} {$player->sum}" . PHP_EOL;
        }

        return $this->replyToChat($text);
    }
}
