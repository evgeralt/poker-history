<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property integer $session_id
 * @property integer $player_id
 */
class SessionPlayers extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%session_players}}';
    }

    public function rules()
    {
        return [
            [['session_id', 'player_id'], 'integer'],
        ];
    }
}
