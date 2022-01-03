<?php

namespace common\cases\session;

use common\models\SessionPlayers;
use common\models\Sessions;
use yii\base\InvalidCallException;
use yii\db\Expression;

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
        return SessionPlayers::find()
            ->select(['*', 'sum' => new Expression('(select sum(amount) from session_transactions where session_id=session_players.session_id)')])
            ->andWhere(['session_id' => $sessionId])
            ->with('user')
            ->all();
    }

    public function savePlayer(int $playerId, int $sessionId): void
    {
        $sessionPlayer = new SessionPlayers();
        $sessionPlayer->session_id = $sessionId;
        $sessionPlayer->player_id = $playerId;
        $sessionPlayer->save();
    }
}
