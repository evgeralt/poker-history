<?php

namespace phpunit\common;

use yii\helpers\ArrayHelper;

class TestCase extends \phpunit\TestCase
{
    protected function getConfig()
    {
        return ArrayHelper::merge(
            self::$config['common/main'],
            self::$config['common/main-local']
        );
    }
}
