<?php

namespace common\cases\session;

use yii\base\InvalidCallException;
use yii\caching\CacheInterface;

class CacheSessionRepo extends AbstractSessionRepo
{
    public const ACTIVE_GAMES_CACHE_KEY = 'activeGameIds';
    public const GAME_PLAYERS_CACHE_KEY = 'gamePlayers';
    /** @var CacheInterface */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getActiveSessions(): array
    {
        return $this->cache->get(self::ACTIVE_GAMES_CACHE_KEY) ?: [];
    }

    public function saveActiveSessions(array $activeSessions): void
    {
        $this->cache->set(self::ACTIVE_GAMES_CACHE_KEY, $activeSessions, 1200);
    }

    public function newSession(int $initiatorId, int $chatId)
    {
        throw new InvalidCallException('Not allowed');
    }

    public function getPlayers(int $sessionId): array
    {
        return $this->cache->get(self::GAME_PLAYERS_CACHE_KEY . $sessionId) ?: [];
    }

    public function savePlayers(array $playerIds, int $sessionId): void
    {
        $players = $this->getPlayers($sessionId);
        foreach ($playerIds as $playerId => $playerName) {
            $players[$playerId] = $playerName;

        }
        $this->cache->set(self::GAME_PLAYERS_CACHE_KEY . $sessionId, $players, 120);
    }
}
