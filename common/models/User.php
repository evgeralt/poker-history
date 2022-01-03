<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $is_bot
 * @property integer $is_telegram
 * @property string  $first_name
 * @property string  $last_name
 * @property string  $username
 * @property string  $language_code
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name . " (@{$this->username})";
    }
}
