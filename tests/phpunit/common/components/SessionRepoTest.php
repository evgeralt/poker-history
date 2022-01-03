<?php

namespace phpunit\common\components;

use common\cases\session\CacheSessionRepo;
use common\cases\session\DbSessionRepo;
use common\cases\session\SessionRepo;
use common\models\Sessions;
use phpunit\common\TestCase;
use yii\base\Exception;

class SessionRepoTest extends TestCase
{
    public function testGetSessionId()
    {
        $sessionRepo = $this->getSessionRepo();

        // Создаем сессию
        $sessionRepo->newSession(1, 1);
        $sessionId = $sessionRepo->getSessionId(1);
        // Сессия создалась, её ID можно получить
        $this->assertIsInt($sessionId);
        $this->assertSame([1], array_keys($sessionRepo->getActiveSessions()));

        // Очищаем кэш, проверяем, что без кэша всё отрабатывает корректно
        \Yii::$app->cache->flush();
        $this->assertSame($sessionId, $sessionRepo->getSessionId(1));

        // Проверяем запись в кэш
        $this->assertSame($sessionId, Sessions::findOne($sessionId)->id);
        Sessions::deleteAll(['id' => $sessionId]);
        $this->assertNull(Sessions::findOne($sessionId));
        $this->assertSame($sessionId, $sessionRepo->getSessionId(1));
        \Yii::$app->cache->flush();

        // Теперь сессии нет ни в базе ни в кэше, ловим ошибку
        $this->expectException(Exception::class);
        $sessionRepo->getSessionId(1);
    }

    public function testGetPlayers()
    {
        $sessionRepo = $this->getSessionRepo();

        $sessionRepo->newSession(1, 1);
        $sessionId = $sessionRepo->getSessionId(1);
        $this->assertSame([], $sessionRepo->getPlayers($sessionId));

        $sessionRepo->savePlayers([1 => 1, 2 => 2], $sessionId);
        $this->assertSame([1 => 1, 2 => 2], $sessionRepo->getPlayers($sessionId));
    }

    /**
     * @return SessionRepo
     */
    private function getSessionRepo()
    {
        return new SessionRepo(
            new CacheSessionRepo(\Yii::$app->cache),
            new DbSessionRepo()
        );
    }
}
