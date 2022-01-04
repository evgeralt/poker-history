<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%player_friends}}`.
 */
class m220103_210156_create_player_friends_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player_friends}}', [
            'player_id' => $this->bigInteger(20)->notNull(),
            'friend_id' => $this->bigInteger(20)->notNull(),
        ]);

        $this->addPrimaryKey('pk_player_friends', 'player_friends', ['player_id', 'friend_id']);
        $this->addForeignKey('fk_player_friends_player_id', 'player_friends', 'player_id', 'user', 'id');
        $this->addForeignKey('fk_player_friends_friend_id', 'player_friends', 'friend_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%player_friends}}');
    }
}
