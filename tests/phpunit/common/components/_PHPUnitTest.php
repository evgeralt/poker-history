<?php

namespace phpunit\common\components;

use phpunit\common\TestCase;
use yii\base\Application;

/**
 * Проверка успешного запуска PHPUnit.
 */
class _PHPUnitTest extends TestCase
{
    public function testBase()
    {
        $this->assertTrue(true);
        $this->assertInstanceOf(Application::class, \Yii::$app);
    }
}
