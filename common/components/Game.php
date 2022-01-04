<?php

namespace common\components;

use common\cases\session\DbSessionRepo;
use common\cases\session\Session;
use common\models\User;
use yii\base\Component;
use yii\caching\CacheInterface;
use yii\db\Expression;
use yii\di\Instance;

class Game extends Component
{
    /** @var CacheInterface */
    public $cache = 'cache';

    public function init()
    {
        parent::init();

        $this->cache = Instance::ensure($this->cache, CacheInterface::class);
    }

    public function instanceSession(int $chatId): Session
    {
        return new Session(new DbSessionRepo(), $chatId);
    }

    public function createPlayer(string $name): int
    {
        $minId = User::find()->select('min(id)')->scalar();
        if (!$minId || $minId > 0) {
            $minId = -1;
        } else {
            $minId--;
        }
        $user = new User();
        $user->id = $minId;
        $user->first_name = $name;
        $user->is_telegram = 0;
        $user->save();

        return $user->id;
    }

    public function getPlayer(int $playerId): User
    {
        return User::findOne(['id' => $playerId]);
    }

    /**
     * @param int $senderId
     * @param int $chatId
     *
     * @return User[]
     */
    public function playerListForAdd(int $senderId, int $chatId): array
    {
        $sql = <<<SQL
select player_id
from session_players
where session_id in (select id from sessions where chat_id = $chatId or player_id=$senderId) and player_id != $senderId
SQL;

        return User::find()
            ->where(new Expression("id in ($sql)"))
            ->all();
    }
}
