<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
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
        $conversation = new Conversation($this->getMessage()->getFrom()->getId(), $this->getMessage()->getChat()->getId(), 'manage');
        $conversation->notes['action'] = 'deposit';
        $conversation->update();

        return $this->replyToChat('На какую сумму пополнить?');
    }
}
