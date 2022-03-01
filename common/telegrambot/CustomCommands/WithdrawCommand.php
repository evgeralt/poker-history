<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

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
        $conversation = new Conversation($this->getMessage()->getFrom()->getId(), $this->getMessage()->getChat()->getId(), 'manage');
        $conversation->notes['action'] = 'withdraw';
        $conversation->update();

        return $this->replyToChat(
            'Сколько снять?', [
                'reply_markup' => Keyboard::forceReply(),
            ]
        );    }
}
