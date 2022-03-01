<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $session_id
 * @property integer $player_id
 * @property float $amount
 * @property integer $created_at
 * @property integer $updated_at
 */
class SessionTransactions extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%session_transactions}}';
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
            [['session_id', 'player_id', 'amount'], 'required'],
            [['session_id', 'player_id'], 'integer'],
        ];
    }
}
