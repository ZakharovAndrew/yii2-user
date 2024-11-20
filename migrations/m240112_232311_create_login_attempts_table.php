<?php

use yii\db\Migration;

class m240112_232311_create_login_attempts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('login_attempts', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'ip_address' => $this->string()->notNull(),
            'attempt_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'successful' => $this->boolean()->notNull()->defaultValue(false),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('login_attempts');
    }
}