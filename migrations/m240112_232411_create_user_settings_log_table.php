<?php

use yii\db\Migration;

class m240112_232411_create_user_settings_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_settings_log', [
            'id' => $this->primaryKey(),
            'user_settings_id' => $this->integer()->notNull(),
            'changed_by' => $this->integer()->notNull(),
            'changed_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'old_value' => $this->string(500)->null(),
            'new_value' => $this->string(500)->notNull(),
        ]);

        // Add foreign key
        $this->addForeignKey(
            'fk-user_settings_log-user_settings_id',
            'user_settings_log',
            'user_settings_id',
            'user_settings',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_settings_log-user_settings_id', 'user_settings_log');
        $this->dropTable('user_settings_log');
    }
}