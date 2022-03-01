<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\components\Game;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\ServerResponse;
use Yii;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';
    /**
     * @var string
     */
    protected $description = 'Handle generic message';
    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $fromId = $message->getFrom()->getId();
        $conversation = new Conversation($message->getFrom()->getId(), $message->getChat()->getId());

        /** @var Game $game */
        $game = Yii::$app->game;
        $session = $game->instanceSession($this->getMessage()->getChat()->getId());
        $text = '';
        if ($conversation->exists()) {
            $action = $conversation->notes['action'];
            switch ($action) {
                case "deposit":
                case "withdraw":
                    $sum = (int)$message->getText();
                    if ($sum === 0) {
                        $text = 'Неверно указана сумма!';
                        break;
                    }
                    $playerId = $this->getMessage()->getFrom()->getId();
                    if (isset($conversation->notes['managePlayerId'])) {
                        $playerId = $conversation->notes['managePlayerId'];
                    }
                    if (!$session->isJoined($playerId)) {
                        $session->join($playerId);
                    }
                    $text = $game->getPlayer($playerId)->getFullName();
                    if ($action === 'deposit') {
                        $session->transaction($playerId, -$sum);
                        $text .= " пополнил на $sum";
                    } else {
                        $session->transaction($playerId, $sum);
                        $text .= " снял $sum";
                    }
                    $text .= PHP_EOL . "Банк: " . $session->bankSum();

                    break;
                case 'createPlayer':
                    $playerName = $message->getText();
                    $text = $playerName . ' присоединился к игре';
                    $playerId = $game->createPlayer($playerName);
                    $session->join($playerId);

                    break;
            }

            $conversation->stop();
        }

        return $this->replyToChat(
            $text, [
                'reply_markup' => GoCommand::startScreen(),
            ]
        );
    }
}
