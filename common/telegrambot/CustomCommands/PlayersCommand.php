<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class PlayersCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'players';
    /**
     * @var string
     */
    protected $description = 'Players manage';
    /**
     * @var string
     */
    protected $usage = '/players';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        return $this->replyToChat('', [
            'text' => 'Players menu',
            'reply_markup' => new InlineKeyboard(self::mainMenu()),
        ]);
    }

    public static function mainMenu(): array
    {
        return [
            ['text' => 'Add player', 'callback_data' => 'addPlayer'],
            ['text' => 'Show players', 'callback_data' => 'showPlayers'],
        ];
    }
}
