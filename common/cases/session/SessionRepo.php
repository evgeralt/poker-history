<?php

namespace common\cases\session;

use yii\base\Exception;

class SessionRepo extends AbstractSessionRepo
{
    /** @var SessionRepoInterface */
    private $repoSlave;
    /** @var SessionRepoInterface */
    private $repoMaster;

    public function __construct(SessionRepoInterface $repoSlave, SessionRepoInterface $repoMaster)
    {
        $this->repoSlave = $repoSlave;
        $this->repoMaster = $repoMaster;
    }

    public function getSessionId(int $chatId): int
    {
        $activeSessions = $this->getActiveSessions();
        if (isset($activeSessions[$chatId])) {
            return $activeSessions[$chatId];
        }

        throw new Exception('Session id is not found');
    }

    public function getActiveSessions(): array
    {
        $activeSessions = $this->repoSlave->getActiveSessions();
        if ($activeSessions) {
            return $activeSessions;
        }
        $activeSessions = $this->repoMaster->getActiveSessions();
        $this->repoSlave->saveActiveSessions($activeSessions);

        return $activeSessions;
    }

    public function newSession(int $initiatorId, int $chatId)
    {
        $this->repoMaster->newSession($initiatorId, $chatId);
        $this->repoSlave->saveActiveSessions($this->repoMaster->getActiveSessions());
    }

    public function saveActiveSessions(array $activeSessions): void
    {
        $this->repoSlave->saveActiveSessions($activeSessions);
    }

    public function getPlayers(int $sessionId): array
    {
        return $this->repoSlave->getPlayers($sessionId);
    }

    public function savePlayers(array $playerIds, int $sessionId): void
    {
        $this->repoMaster->savePlayers($playerIds, $sessionId);
        $this->repoSlave->savePlayers($playerIds, $sessionId);
    }
}
