<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property integer $session_id
 * @property integer $player_id
 *
 * @property User    $user
 */
class SessionPlayers extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%session_players}}';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'player_id']);
    }

    public function rules()
    {
        return [
            [['session_id', 'player_id'], 'integer'],
            ['session_id', 'unique', 'targetAttribute' => ['session_id', 'player_id']],
        ];
    }
}
