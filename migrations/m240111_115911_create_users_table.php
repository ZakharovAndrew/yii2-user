<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m240111_115911_create_users_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'users',
            [
                'id' => $this->primaryKey(),
                'username' => $this->string(190)->notNull()->unique(),
                'auth_key' => $this->string(32)->defaultValue(null),
                'password' => $this->string(255)->notNull(),
                'password_reset_token' => $this->string(190)->defaultValue(null)->unique(),
                'email' => $this->string(190)->notNull()->unique(),
                'name' => $this->string(255)->notNull(),
                'avatar' => $this->string(255)->defaultValue(null),
                'city' => $this->string(150)->defaultValue(null),
                'birthday' => $this->dateTime()->defaultValue(null),
                'status' => $this->integer()->defaultValue(10),
                'sex' => $this->integer()->defaultValue(0),
                'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
                'updated_at' => $this->dateTime()
            ]
        );
        
        // creates index for column `email`
        $this->createIndex(
            'idx-users-email',
            'users',
            'email'
        );

        $this->insert('users', [
            'username' => 'admin',
            'name' => 'Admin',
            'password' => '$2y$13$4j5gDqNVFWnGWFVTQoclaeLTZR62BJ99wtF0N3ohc6UVJZPpUZT3a', // demo123
            'status' => '40',
            'email' => 'please-change-email@test.com',
        ]);
    }

    public function down()
    {
        $this->dropTable('users');
    }
}
