<?php

use yii\db\Migration;

class m250223_111113_create_verification_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%verification_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'email' => $this->string(150)->notNull(),
            'action' => $this->tinyInteger()->notNull()->comment('1 - send_code, 2 - verify_code'),
            'ip_address' => $this->string(45)->notNull(),
            'user_agent' => $this->string(500),
            'status' => $this->tinyInteger()->notNull()->comment('1 - success, 2 - failed'),
            'error_message' => $this->string(1000),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') )
        ]);
        
        // Indexes for fast search
        $this->createIndex('idx_verification_log_email', '{{%verification_log}}', 'email');
        $this->createIndex('idx_verification_log_user_id', '{{%verification_log}}', 'user_id');
        $this->createIndex('idx_verification_log_created_at', '{{%verification_log}}', 'created_at');
        $this->createIndex('idx_verification_log_action_status', '{{%verification_log}}', ['action', 'status']);
        $this->createIndex('idx_verification_log_email_created', '{{%verification_log}}', ['email', 'created_at']);
        
        // Table comment
        $this->addCommentOnTable('{{%verification_log}}', 'Log of verification code requests and attempts');
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%verification_log}}');
    }
}