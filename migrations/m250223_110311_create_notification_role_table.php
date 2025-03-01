<?php

use yii\db\Migration;

class m250223_110311_create_notification_role_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('notification_role', [
            'notification_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
            'PRIMARY KEY(notification_id, role_id)',
        ]);

        // Добавляем внешние ключи
        $this->addForeignKey(
            'fk-notification_role-notification_id',
            'notification_role',
            'notification_id',
            'notifications',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-notification_role-role_id',
            'notification_role',
            'role_id',
            'roles',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Удаляем внешние ключи
        $this->dropForeignKey('fk-notification_role-notification_id', 'notification_role');
        $this->dropForeignKey('fk-notification_role-role_id', 'notification_role');

        // Удаляем таблицу
        $this->dropTable('notification_role');
    }
}