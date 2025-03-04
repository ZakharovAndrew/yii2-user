<?php

use yii\db\Migration;

class m250223_110111_create_user_notification_settings_table extends Migration
{
    public function up()
    {
        $this->createTable('user_notification_settings', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(), // ID пользователя
            'notification_id' => $this->integer()->notNull(), // ID уведомления
            'send_email' => $this->boolean()->defaultValue(false), // Разрешить отправку по Email
            'send_telegram' => $this->boolean()->defaultValue(false), // Разрешить отправку в Telegram
            'send_push' => $this->boolean()->defaultValue(false), // Разрешить отправку Push-уведомлений
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Добавляем индексы для оптимизации запросов
        $this->createIndex('idx-user_notification_settings-user_id', 'user_notification_settings', 'user_id');
        $this->createIndex('idx-user_notification_settings-notification_id', 'user_notification_settings', 'notification_id');

        // Добавляем внешние ключи
        $this->addForeignKey(
            'fk-user_notification_settings-user_id',
            'user_notification_settings',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_notification_settings-notification_id',
            'user_notification_settings',
            'notification_id',
            'notifications',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk-user_notification_settings-user_id', 'user_notification_settings');
        $this->dropForeignKey('fk-user_notification_settings-notification_id', 'user_notification_settings');

        $this->dropIndex('idx-user_notification_settings-user_id', 'user_notification_settings');
        $this->dropIndex('idx-user_notification_settings-notification_id', 'user_notification_settings');

        $this->dropTable('user_notification_settings');
    }
}
