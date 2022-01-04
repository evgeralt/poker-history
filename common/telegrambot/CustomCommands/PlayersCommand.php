<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

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
        /** @var Game $game */
        $game = Yii::$app->game;
        $session = $game->instanceSession($this->getMessage()->getChat()->getId());

        $menu = [];
        $players = $session->getPlayers();
        $row = [];
        foreach ($players as $player) {
            $row[] = ['text' => $player->user->getFullName() . ' ' . ($player->sum ?: 0), 'callback_data' => "editPlayer{$player->player_id}"];
            if (count($row) === 2) {
                $menu[] = $row;
                $row = [];
            }
        }
        if ($row) {
            $menu[] = $row;
        }
        $menu[] = [['text' => 'Добавить игрока', 'callback_data' => 'selectForAddPlayer']];

        return $this->replyToChat('', [
            'text' => 'Игроки текущей сессии',
            'reply_markup' => new InlineKeyboard(...$menu),
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
