<?php

namespace phpunit\common\components;

use common\cases\session\CacheSessionRepo;
use common\cases\session\DbSessionRepo;
use common\cases\session\Session;
use common\cases\session\SessionRepo;
use phpunit\common\TestCase;

class SessionTest extends TestCase
{
    public function testBase()
    {
        $session = $this->getSession();

        $session->newSession(1, 1);
        $this->assertIsInt($session->getSessionId());

        $this->assertSame([], $session->getPlayers());
        $this->assertSame([], $session->getTransactionsSummary());

        $session->join([1 => 1]);
        $this->assertSame(1, $session->getPlayers()[0]->player_id);

        $session->transaction(1, -100);
        $this->assertSame(100.0, $session->bankSum());
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        return new Session(new SessionRepo(
            new CacheSessionRepo(\Yii::$app->cache),
            new DbSessionRepo()
        ), 1);
    }
}
