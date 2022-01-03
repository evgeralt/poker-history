<?php

namespace common\components;

use common\cases\session\CacheSessionRepo;
use common\cases\session\DbSessionRepo;
use common\cases\session\Session;
use common\cases\session\SessionRepo;
use yii\base\Component;
use yii\caching\CacheInterface;
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
        return new Session(new SessionRepo(
            new CacheSessionRepo($this->cache),
            new DbSessionRepo(),
        ), $chatId);
    }
}
