<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

class DepositCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'deposit';
    /**
     * @var string
     */
    protected $description = 'Deposit';
    /**
     * @var string
     */
    protected $usage = '/deposit';

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

        $chatId = $this->getMessage()->getChat()->getId();
        $requestAmount = $this->getMessage()->getText(true);
        if (!$requestAmount) {
            $text = 'Select sum';
            $markup = new Keyboard(
                ['/deposit 100', '/deposit 200', '/deposit 300', '/deposit 500'],
                ['/deposit 600', '/deposit 700', '/deposit 800', '/deposit 900'],
                ['/deposit 1000', '/deposit 1200', '/deposit 1500', '/deposit 3000']
            );
        } else {
            $session = $game->instanceSession($chatId);
            if (!$session->isJoined($this->getMessage()->getFrom()->getId())) {
                $session->join($this->getMessage()->getFrom()->getId());
            }
            $session->transaction($this->getMessage()->getFrom()->getId(), -$requestAmount);

            $text = $this->getMessage()->getFrom()->getFirstName() . ' ' . $this->getMessage()->getFrom()->getLastName() . " deposit on $requestAmount" . PHP_EOL;
            $text .= "Bank: " . $session->bankSum();
            $markup = GoCommand::startScreen();
        }
        $markup
            ->setResizeKeyboard(true)
            ->setSelective(false);

        return $this->replyToChat(
            $text, [
                'reply_markup' => $markup,
            ]
        );
    }
}
