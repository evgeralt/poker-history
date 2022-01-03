<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

class GoCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'go';
    /**
     * @var string
     */
    protected $description = 'Game start';
    /**
     * @var string
     */
    protected $usage = '/go';

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
        try {
            $from = $this->getMessage()->getFrom();
            $chatId = $this->getMessage()->getChat()->getId();
            $session = $game->instanceSession($chatId);
            $session->newSession($from->getId(), $this->getMessage()->getChat()->getId());
            $session->join($this->getMessage()->getFrom()->getId());
        } catch (\Throwable $exception) {
//            var_dump($exception->getMessage());
        }

        return $this->replyToChat(
            'Game started', [
                'reply_markup' => self::startScreen(),
            ]
        );
    }

    public static function startScreen(): Keyboard
    {
        $keyboard = new Keyboard(
            ['/deposit 100', '/deposit 200', '/deposit 300', '/deposit 500'],
            ['/deposit 600', '/deposit 700', '/deposit 800', '/deposit 900'],
            ['/deposit 1000', '/deposit 1200', '/deposit 1500', '/deposit 3000'],
            '/info',
            '/player',
        );
        $keyboard
            ->setInputFieldPlaceholder('Deposit')
            ->setResizeKeyboard(true)
            ->setSelective(false);

        return $keyboard;
    }
}
