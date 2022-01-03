<?php

namespace common\cases\session;

use common\models\SessionPlayers;
use common\models\Sessions;
use common\models\SessionTransactions;
use yii\helpers\ArrayHelper;

class Session
{
    /** @var SessionRepoInterface */
    private $repo;
    /** @var int */
    private $chatId;
    /** @var int */
    private $sessionId;

    public function __construct(SessionRepo $repo, int $chatId)
    {
        $this->chatId = $chatId;
        $this->setRepo($repo);
    }

    public function setRepo(SessionRepoInterface $repo)
    {
        $this->repo = $repo;
    }

    public function newSession(int $initiatorId, int $chatId)
    {
        $this->repo->newSession($initiatorId, $chatId);
    }

    public function getSessionId(): int
    {
        if ($this->sessionId) {
            return $this->sessionId;
        }
        $this->sessionId = $this->repo->getSessionId($this->chatId);

        return $this->sessionId;
    }

    public function join(array $playerIds)
    {
        $this->repo->savePlayers($playerIds, $this->getSessionId());
    }

    public function isJoined(int $playerId): bool
    {
        return $this->repo->isJoined($this->getSessionId(), $playerId);
    }

    public function transaction(int $playerId, float $amount): bool
    {
        $sessionTransaction = new SessionTransactions();
        $sessionTransaction->session_id = $this->getSessionId();
        $sessionTransaction->player_id = $playerId;
        $sessionTransaction->amount = $amount;
        $sessionTransaction->save();

        return true;
    }

    public function bankSum(): float
    {
        $bankSum = SessionTransactions::find()->andWhere(['session_id' => $this->getSessionId()])->sum('amount') ?: 0;

        return $bankSum * -1;
    }

    public function end()
    {
        Sessions::updateAll(['status' => Sessions::STATUS_COMPLETED], ['id' => $this->getSessionId()]);
    }

    public function getTransactionsSummary(): array
    {
        $data = SessionTransactions::find()
            ->select(['player_id', 'sum(amount) as sum'])
            ->andWhere(['session_id' => $this->getSessionId()])
            ->groupBy('player_id')
            ->asArray()
            ->all();
        $data = ArrayHelper::map($data, 'player_id', 'sum');
        $players = $this->repo->getPlayers($this->getSessionId());
        $res = [];
        foreach ($players as $playerId => $playerName) {
            $res[$playerName] = $data[$playerId] ?? '?';
        }

        return $res;
    }

    /**
     * @return SessionPlayers[]
     */
    public function getPlayers(): array
    {
        return SessionPlayers::find()
            ->andWhere(['session_id' => $this->getSessionId()])
            ->with('user')
            ->all();
    }
}
