<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

class JoinCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'join';

    /**
     * @var string
     */
    protected $description = 'Game join';

    /**
     * @var string
     */
    protected $usage = '/join';

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
        $from = $this->getMessage()->getFrom();
        $fullName = $from->getFirstName() . ' ' . $from->getLastName();
        $session->join([$from->getId() => $fullName]);

        return $this->replyToChat(
            'Welcome - ' . $from->getFirstName() . ' ' . $from->getLastName()
        );
    }
}
