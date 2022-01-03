<?php

namespace phpunit;

use Yii;
use yii\db\Transaction;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static $config;
    /** @var Transaction */
    private $transaction;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        if (self::$config === null) {
            self::$config = [
                'common/main' => require(YII_APP_BASE_PATH . '/common/config/main.php'),
                'common/main-local' => require(YII_APP_BASE_PATH . '/common/config/main-local.php'),
            ];
        }
    }

    protected function setUp(): void
    {
        $this->startApp();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->transaction->rollBack();
        Yii::$app->cache->flush();
        parent::tearDown();
    }

    protected function startApp()
    {
        $config = $this->getConfig();
        $config['class'] = $this->applicationClass();
        Yii::$app = Yii::createObject($config);

        $this->transaction = Yii::$app->db->beginTransaction();
    }

    protected function applicationClass(): string
    {
        return 'yii\web\Application';
    }

    abstract protected function getConfig();
}
