<?php

namespace common\components;

use common\models\SessionPlayers;
use common\models\Sessions;
use common\models\SessionTransactions;
use yii\base\Component;
use yii\base\Exception;
use yii\caching\CacheInterface;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class Game extends Component
{
    public const ACTIVE_GAMES_CACHE_KEY = 'activeGameIds';
    public const GAME_PLAYERS_CACHE_KEY = 'gamePlayers';
    /** @var CacheInterface */
    public $cache = 'cache';

    public function init()
    {
        parent::init();

        $this->cache = Instance::ensure($this->cache, CacheInterface::class);
    }

    public function start(int $initiatorId, int $chatId): int
    {
        try {
            $sessionId = $this->getSessionId($chatId);
            $this->removeFromActiveGames($chatId, $sessionId);
        } catch (\Throwable $exception) {
            // nothing
        }

        $session = new Sessions();
        $session->initiator_id = $initiatorId;
        $session->chat_id = $chatId;
        $session->save();
        $this->pushToActiveGames($chatId, $session->id);

        return $session->id;
    }

    public function join(int $chatId, array $playerIds)
    {
        $sessionId = $this->getSessionId($chatId);
        $playersCacheKey = self::GAME_PLAYERS_CACHE_KEY . $sessionId;
        $players = $this->cache->get($playersCacheKey) ?: [];

        foreach ($playerIds as $playerId => $playerName) {
            $sessionPlayer = new SessionPlayers();
            $sessionPlayer->session_id = $sessionId;
            $sessionPlayer->player_id = $playerId;
            if ($sessionPlayer->save()) {
                $players[$playerId] = $playerName;
            }
        }
        $this->cache->set($playersCacheKey, $players, 3600 * 24 * 3);
    }

    public function transaction(int $chatId, int $playerId, float $amount): bool
    {
        $this->join($chatId, ['id:' . $playerId => $playerId]);
        $sessionId = $this->getSessionId($chatId);

        $playersCacheKey = self::GAME_PLAYERS_CACHE_KEY . $sessionId;
        $players = $this->cache->get($playersCacheKey) ?: [];
        if (!isset($players[$playerId])) {
            throw new Exception('Player not registered in this game');
        }

        $sessionTransaction = new SessionTransactions();
        $sessionTransaction->session_id = $sessionId;
        $sessionTransaction->player_id = $playerId;
        $sessionTransaction->amount = $amount;
        $sessionTransaction->save();

        return true;
    }

    public function bankSum(int $chatId): float
    {
        $sessionId = $this->getSessionId($chatId);
        $bankSum = SessionTransactions::find()->andWhere(['session_id' => $sessionId])->sum('amount') ?: 0;

        return $bankSum * -1;
    }

    public function end(int $chatId): array
    {
        $sessionId = $this->getSessionId($chatId);
        $playersCacheKey = self::GAME_PLAYERS_CACHE_KEY . $sessionId;
        $players = $this->cache->get($playersCacheKey) ?: [];

        $data = SessionTransactions::find()
            ->select(['player_id', 'sum(amount) as sum'])
            ->andWhere(['session_id' => $sessionId])
            ->groupBy('player_id')
            ->asArray()
            ->all();
        $data = ArrayHelper::map($data, 'player_id', 'sum');
        $res = [];
        foreach ($players as $playerId => $playerName) {
            $res[$playerName] = $data[$playerId] ?? '?';
        }

        Sessions::updateAll(['status' => Sessions::STATUS_COMPLETED], ['id' => $sessionId]);
        $this->cache->delete($playersCacheKey);

        return $res;
    }

    protected function getSessionId(int $chatId): int
    {
        if (isset($this->getActiveGames()[$chatId])) {
            return $this->getActiveGames()[$chatId];
        }
        /** @var Sessions[] $activeGamesModels */
        $activeGamesModels = Sessions::find()
            ->andWhere(['status' => Sessions::STATUS_ACTIVE])
            ->all();
        $activeGames = [];
        foreach ($activeGamesModels as $activeGame) {
            $activeGames[$activeGame->chat_id] = $activeGame->id;
        }
        $this->cache->set(self::ACTIVE_GAMES_CACHE_KEY, $activeGames, 1200);
        if (isset($activeGames[$chatId])) {
            return $activeGames[$chatId];
        }

        throw new Exception('Session id is not found');
    }

    protected function getActiveGames(): array
    {
        return $this->cache->get(self::ACTIVE_GAMES_CACHE_KEY) ?: [];
    }

    protected function removeFromActiveGames(int $chatId, int $sessionId): void
    {
        Sessions::updateAll(['status' => Sessions::STATUS_COMPLETED], ['id' => $sessionId]);
        $activeGames = $this->getActiveGames();
        unset($activeGames[$chatId]);
        $this->cache->set(self::ACTIVE_GAMES_CACHE_KEY, $activeGames, 1200);
    }

    protected function pushToActiveGames(int $chatId, int $sessionId): void
    {
        $activeGames = $this->getActiveGames();
        $activeGames[$chatId] = $sessionId;
        $this->cache->set(self::ACTIVE_GAMES_CACHE_KEY, $activeGames, 1200);
    }
}
