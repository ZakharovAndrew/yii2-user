<?php

use yii\db\Migration;

class m250223_110111_create_user_notification_settings_table extends Migration
{
    public function up()
    {

        $this->createTable('user_notification_settings', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'notification_group_id' => $this->integer()->notNull(),
            'telegram' => $this->boolean()->notNull()->defaultValue(true),
            'email' => $this->boolean()->notNull()->defaultValue(true),
            'push' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        
        // foreign key for user_id
        $this->addForeignKey(
            'fk-user_notification_settings-user_id',
            'user_notification_settings',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        // foreign key for notification_group_id
        $this->addForeignKey(
            'fk-user_notification_settings-notification_group_id',
            'user_notification_settings',
            'notification_group_id',
            'notification_groups',
            'id',
            'CASCADE'
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk-user_notification_settings-user_id', 'user_notification_settings');
        $this->dropForeignKey('fk-user_notification_settings-notification_group_id', 'user_notification_settings');

        $this->dropTable('user_notification_settings');
    }
}
