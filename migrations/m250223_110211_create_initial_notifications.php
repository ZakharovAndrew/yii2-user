<?php

use yii\db\Migration;

class m250223_110211_create_initial_notifications extends Migration
{
    public function up()
    {
        $this->insert('notification_groups', [
            'name' => 'Main Notifications',
            'position' => 1,
        ]);

        // Получаем ID созданной группы
        //$notificationGroupId = $this->db->lastInsertId();
        $lastId = Yii::$app->db->getLastInsertID();

        $this->insert('notifications', [
            'name' => 'Happy Birthday Greeting',
            'notification_group_id' => $lastId,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->delete('notifications', ['name' => 'Happy Birthday Greeting']);

        $this->delete('notification_groups', ['name' => 'Main Notifications']);
    }
}
