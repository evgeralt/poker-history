<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $status
 * @property integer $initiator_id
 * @property integer $chat_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Sessions extends ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_COMPLETED = 0;

    public static function tableName()
    {
        return '{{%sessions}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['initiator_id', 'chat_id'], 'required'],
            [['status', 'initiator_id', 'chat_id'], 'integer'],
        ];
    }
}
