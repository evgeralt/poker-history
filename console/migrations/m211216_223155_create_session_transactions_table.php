<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%session_transactions}}`.
 */
class m211216_223155_create_session_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%session_transactions}}', [
            'id' => $this->primaryKey(),
            'session_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->notNull(),
            'amount' => $this->float()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_session_transactions_session_id', 'session_transactions', 'session_id', 'sessions', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%session_transactions}}');
    }
}
