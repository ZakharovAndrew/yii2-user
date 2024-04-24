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
                'username' => $this->string(255)->notNull()->unique(),
                'auth_key' => $this->string(32)->defaultValue(null),
                'password' => $this->string(255)->notNull(),
                'password_reset_token' => $this->string(255)->defaultValue(null)->unique(),
                'email' => $this->string(255)->notNull()->unique(),
                'name' => $this->string(255)->notNull(),
                'avatar' => $this->string(255)->defaultValue(null),
                'city' => $this->string(150)->defaultValue(null),
                'birthday' => $this->dateTime()->defaultValue(null),
                'status' => $this->integer()->defaultValue(10),
                'sex' => $this->integer()->defaultValue(0),
                'created_at' => $this->dateTime()->defaultValue( new \yii\db\Expression('NOW()') ),
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
