<?php

use yii\db\Migration;

class m250223_111118_create_reminder_tables extends Migration
{
    public function safeUp()
    {
        // Reminders table
        $this->createTable('{{%reminders}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('Reminder recipient'),
            'created_by' => $this->integer()->notNull()->comment('Reminder creator'),
            'description' => $this->text()->comment('Description'),
            'remind_at' => $this->dateTime()->notNull()->comment('Reminder time'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Status: 1-active, 2-completed, 3-cancelled'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Creation date'),
        ]);

        $this->createIndex('idx_reminder_user_status_time', '{{%reminders}}', ['user_id', 'status', 'remind_at']);
        
        // Foreign keys (they automatically create indexes)
        $this->addForeignKey('fk_reminder_user', '{{%reminders}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_reminder_created_by', '{{%reminders}}', 'created_by', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_reminder_user', '{{%reminders}}');
        $this->dropForeignKey('fk_reminder_created_by', '{{%reminders}}');
        $this->dropTable('{{%reminders}}');
    }
}