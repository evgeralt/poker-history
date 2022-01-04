<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%session_players}}`.
 */
class m211216_222945_create_session_players_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%session_players}}', [
            'session_id' => $this->integer()->notNull(),
            'player_id' => $this->bigInteger(20)->notNull(),
        ]);

        $this->addPrimaryKey('pk_session_players', 'session_players', ['session_id', 'player_id']);
        $this->addForeignKey('fk_session_players_session_id', 'session_players', 'session_id', 'sessions', 'id');
        $this->addForeignKey('fk_session_players_player_id', 'session_players', 'player_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%session_players}}');
    }
}
