<?php

use yii\db\Migration;

/**
 * Class m250223_111111_create_user_controller_log_table
 */
class m250223_111111_create_user_controller_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_controller_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'controller' => $this->string(255)->notNull(),
            'action' => $this->string(255)->notNull(),
            'method' => $this->string(10)->null(),
            'url' => $this->string(255)->null(),
            'request_params' => $this->text()->null(),
            'response_code' => $this->integer()->defaultValue(200),
            'execution_time' => $this->decimal(10, 4)->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip_address' => $this->string(45)->null(),
            'user_agent' => $this->string(255)->null(),
        ]);

        // Add indexes for query optimization
        $this->createIndex('idx_user_controller_log_user_id', 'user_controller_log', 'user_id');
        $this->createIndex('idx_user_controller_log_controller_action', 'user_controller_log', ['controller', 'action']);
        $this->createIndex('idx_user_controller_log_created_at', 'user_controller_log', 'created_at');
        $this->createIndex('idx_user_controller_log_response_code', 'user_controller_log', 'response_code');
        
        // Foreign key for user_id
        $this->addForeignKey(
            'fk_user_controller_log_user_id',
            'user_controller_log',
            'user_id',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_controller_log_user_id', 'user_controller_log');
        $this->dropTable('user_controller_log');
    }
}