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
        $fromFullName = $message->getFrom()->getFirstName() . ' ' . $message->getFrom()->getLastName();
        $conversation = new Conversation($message->getFrom()->getId(), $message->getChat()->getId());

        /** @var Game $game */
        $game = Yii::$app->game;
        $session = $game->instanceSession($this->getMessage()->getChat()->getId());
        $text = '';
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            switch ($command) {
                case "deposit":
                case "withdraw":
                    $sum = (int)$message->getText();
                    if ($sum === 0) {
                        $text = 'Неверно указана сумма!';
                        break;
                    }
                    if (!$session->isJoined($this->getMessage()->getFrom()->getId())) {
                        $session->join($this->getMessage()->getFrom()->getId());
                    }
                    $text = $fromFullName;
                    if ($command === 'deposit') {
                        $session->transaction($fromId, -$sum);
                        $text .= " пополнил на $sum";
                    } else {
                        $session->transaction($fromId, $sum);
                        $text .= " снял $sum";
                    }
                    $text .= PHP_EOL . "Банк: " . $session->bankSum();

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
