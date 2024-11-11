<?php

use yii\db\Migration;

class m240112_232211_create_user_activity_table extends Migration
{
     /**
     * {@inheritdoc}
     */
    public function safeUp()
    {        
        $this->createTable('user_activity', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'date_at' => $this->date()->notNull(),
            'start_activity' => $this->timestamp()->Null(),
            'stop_activity' => $this->timestamp()->Null(),
        ]);
        
        // Adding a composite index on user_id and date_at
        $this->createIndex(
            'idx_user_activity_user_id_date_at',
            'user_activity',
            ['user_id', 'date_at'],
            true // Unique index
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop the composite index
        $this->dropIndex('idx_user_activity_user_id_date_at', 'user_activity');
        
        $this->dropTable('user_activity');
    }
}