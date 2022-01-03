<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Yii;

class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';
    /**
     * @var string
     */
    protected $description = 'Handle the callback query';
    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws \Exception
     */
    public function execute(): ServerResponse
    {
        /** @var Game $game */
        $game = Yii::$app->game;
        $session = $game->instanceSession($this->getCallbackQuery()->getMessage()->getChat()->getId());
        $callback_query = $this->getCallbackQuery();
        $callbackData = $callback_query->getData();

        $data = [
            'chat_id' => $this->getCallbackQuery()->getMessage()->getChat()->getId(),
            'message_id' => $this->getCallbackQuery()->getMessage()->getMessageId(),
            'text' => 'Players menu',
            'reply_markup' => new InlineKeyboard([
                ['text' => 'Add player', 'callback_data' => 'addPlayer'],
                ['text' => 'Show players', 'callback_data' => 'showPlayer'],
            ]),
        ];
        if ($callbackData === 'showPlayers') {
            $menu = [];
            $players = $session->getPlayers();
            $row = [];
            foreach ($players as $player) {
                $row[] = ['text' => $player->user->getFullName(), 'callback_data' => "edit{$session->getSessionId()}-{$player->player_id}"];
                if (count($row) === 2) {
                    $menu[] = $row;
                    $row = [];
                }
            }
            if ($row) {
                $menu[] = $row;
            }

            $menu[] = [['text' => 'Main menu', 'callback_data' => 'mainMenu']];
            $data['reply_markup'] = new InlineKeyboard(...$menu);
        } elseif ($callbackData === 'mainMenu') {
            $data['reply_markup'] = new InlineKeyboard(PlayersCommand::mainMenu());
        }

        Request::editMessageText($data);

        return Request::emptyResponse();
    }
}
