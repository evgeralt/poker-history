<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

class WithdrawCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'withdraw';
    /**
     * @var string
     */
    protected $description = 'Withdraw';
    /**
     * @var string
     */
    protected $usage = '/withdraw';

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

        $requestAmount = $this->getMessage()->getText(true);
        if (!$requestAmount) {
            return $this->replyToChat('');
        } else {
            $chatId = $this->getMessage()->getChat()->getId();
            $game->transaction($chatId, $this->getMessage()->getFrom()->getId(), $requestAmount);

            $text = $this->getMessage()->getFrom()->getFirstName() . ' ' . $this->getMessage()->getFrom()->getLastName() . " withdraw a $requestAmount" . PHP_EOL;
            $text .= "Bank: " . $game->bankSum($chatId);

            return $this->replyToChat(
                $text, [
                    'reply_markup' => GoCommand::startScreen(),
                ]
            );
        }
    }
}
