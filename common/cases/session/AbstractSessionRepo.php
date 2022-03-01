<?php

namespace common\cases\session;

use yii\base\Exception;

abstract class AbstractSessionRepo implements SessionRepoInterface
{
    public function getSessionId(int $chatId): int
    {
        $activeSessions = $this->getActiveSessions();
        if (isset($activeSessions[$chatId])) {
            return $activeSessions[$chatId];
        }

        throw new Exception('Session id is not found');
    }

    public function isJoined(int $sessionId, int $playerId): bool
    {
        return isset($this->getPlayers($sessionId)[$playerId]);
    }
}
