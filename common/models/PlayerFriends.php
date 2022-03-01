<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property integer $player_id
 * @property integer $friend_id
 *
 * @property User    $user
 * @property User    $friend
 * @property User[]  $friends
 */
class PlayerFriends extends ActiveRecord
{
    public $sum;

    public static function tableName()
    {
        return '{{%player_friends}}';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'player_id']);
    }

    public function getFriend()
    {
        return $this->hasOne(User::class, ['id' => 'friend_id']);
    }

    public function rules()
    {
        return [
            [['player_id', 'friend_id'], 'required'],
            [['player_id', 'friend_id'], 'integer'],
            ['player_id', 'unique', 'targetAttribute' => ['player_id', 'friend_id']],
        ];
    }
}
