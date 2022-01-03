<?php

namespace common\cases\session;

use common\models\SessionPlayers;
use common\models\Sessions;
use yii\base\InvalidCallException;

class DbSessionRepo extends AbstractSessionRepo
{
    public function getActiveSessions(): array
    {
        /** @var Sessions[] $activeGamesModels */
        $activeGamesModels = Sessions::find()
            ->andWhere(['status' => Sessions::STATUS_ACTIVE])
            ->all();
        $activeGames = [];
        foreach ($activeGamesModels as $activeGame) {
            $activeGames[$activeGame->chat_id] = $activeGame->id;
        }

        return $activeGames;
    }

    public function saveActiveSessions(array $activeSessions): void
    {
        throw new InvalidCallException('Not allowed');
    }

    public function newSession(int $initiatorId, int $chatId)
    {
        $session = new Sessions();
        $session->initiator_id = $initiatorId;
        $session->chat_id = $chatId;
        $session->save();
    }

    public function getPlayers(int $sessionId): array
    {
        return SessionPlayers::findAll(['session_id' => $sessionId]);
    }

    public function savePlayers(array $playerIds, int $sessionId): void
    {
        foreach ($playerIds as $playerId => $playerName) {
            $sessionPlayer = new SessionPlayers();
            $sessionPlayer->session_id = $sessionId;
            $sessionPlayer->player_id = $playerId;
            $sessionPlayer->save();
        }
    }
}
