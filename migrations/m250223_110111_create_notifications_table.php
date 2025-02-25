<?php

use yii\db\Migration;

class m250223_110111_create_notifications_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('notifications', [
            'id' => $this->primaryKey(),
            'notification_group_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'description' => $this->text()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        
        // Add foreign key       
        $this->addForeignKey(
            'fk-notifications-group_id',
            'notifications',
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
        $this->dropForeignKey('fk-notifications-group_id', 'notifications');
        $this->dropTable('notifications');
    }
}
