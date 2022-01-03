<?php

namespace common\cases\session;

interface SessionRepoInterface
{
    public function getSessionId(int $chatId): int;

    public function getActiveSessions(): array;

    public function saveActiveSessions(array $activeSessions): void;

    public function newSession(int $initiatorId, int $chatId);

    public function getPlayers(int $sessionId): array;

    public function savePlayers(array $playerIds, int $sessionId): void;

    public function isJoined(int $sessionId, int $playerId): bool;
}
