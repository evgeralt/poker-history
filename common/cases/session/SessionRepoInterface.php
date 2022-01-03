<?php

namespace common\cases\session;

use common\models\SessionPlayers;

interface SessionRepoInterface
{
    public function getSessionId(int $chatId): int;

    public function getActiveSessions(): array;

    public function saveActiveSessions(array $activeSessions): void;

    public function newSession(int $initiatorId, int $chatId);

    /**
     * @param int $sessionId
     *
     * @return SessionPlayers[]
     */
    public function getPlayers(int $sessionId): array;

    public function savePlayer(int $playerId, int $sessionId): void;

    public function isJoined(int $sessionId, int $playerId): bool;
}
