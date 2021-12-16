<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Bot\helpers\MessageHelper;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class GameCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'game';

    /**
     * @var string
     */
    protected $description = 'Начало игровой сессии';

    /**
     * @var string
     */
    protected $usage = '/game';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        return Request::emptyResponse();
    }
}
