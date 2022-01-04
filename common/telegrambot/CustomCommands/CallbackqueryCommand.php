<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
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
        $callbackQuery = $this->getCallbackQuery();
        $callbackData = $callbackQuery->getData();
        $fromId = $callbackQuery->getFrom()->getId();

        $data = [
            'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
            'message_id' => $callbackQuery->getMessage()->getMessageId(),
            'text' => 'Players menu',
        ];
        if ($callbackData === 'selectForAddPlayer') {
            $data['text'] = 'Выберите игрока или создайте виртуальный профиль (для игроков без Telegram)';

            $menu = [];
            $row = [];
            foreach ($game->playerListForAdd($fromId, $session->getChatId()) as $player) {
                $row[] = ['text' => $player->getFullName(), 'callback_data' => "addPlayer{$player->id}"];
                if (count($row) === 3) {
                    $menu[] = $row;
                    $row = [];
                }
            }
            if ($row) {
                $menu[] = $row;
            }
            $menu[] = [['text' => 'Создать профиль', 'callback_data' => 'createPlayer']];
            $data['reply_markup'] = new InlineKeyboard(...$menu);
        } elseif ($callbackData === 'createPlayer') {
            $conversation = new Conversation($callbackQuery->getFrom()->getId(), $callbackQuery->getMessage()->getChat()->getId(), 'manage');
            $conversation->notes['action'] = 'createPlayer';
            $conversation->update();
            Request::sendMessage([
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'text' => 'Введите имя игрока',
            ]);
            Request::deleteMessage([
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'message_id' => $callbackQuery->getMessage()->getMessageId(),
            ]);
        } elseif (str_contains($callbackData, 'editPlayer') !== false) {
            $playerId = str_replace('editPlayer', '', $callbackData);
            $player = $game->getPlayer($playerId);
            $conversation = new Conversation($callbackQuery->getFrom()->getId(), $callbackQuery->getMessage()->getChat()->getId(), 'manage');
            $conversation->notes['managePlayerId'] = $playerId;
            $conversation->update();

            $data['text'] = 'Управление пользователем ' . $player->getFullName();
            Request::sendMessage([
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'text' => 'Сейчас вы управляете пользователем ' . $player->getFullName() . ' пополнения и списания будут в счёт этого игрока, что бы отменить выполните любую операцию или нажмите кнопку выше',
                'reply_markup' => GoCommand::startScreen(),
            ]);
            $data['reply_markup'] = new InlineKeyboard([
                ['text' => 'Отменить управление пользователем', 'callback_data' => 'cancelConversation'],
            ]);
        } elseif (str_contains($callbackData, 'addPlayer') !== false) {
            $playerId = str_replace('addPlayer', '', $callbackData);
            $player = $game->getPlayer($playerId);
            $session->join($playerId);
            Request::sendMessage([
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'text' => 'Игрок ' . $player->getFullName() . ' добавлен в игру',
            ]);
            Request::deleteMessage([
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'message_id' => $callbackQuery->getMessage()->getMessageId(),
            ]);
        } elseif ($callbackData === 'cancelConversation') {
            $conversation = new Conversation($callbackQuery->getFrom()->getId(), $callbackQuery->getMessage()->getChat()->getId());
            $conversation->cancel();
        }
        Request::editMessageText($data);

        return Request::emptyResponse();
    }
}
