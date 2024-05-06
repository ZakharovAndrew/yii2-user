<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_roles`.
 */
class m240112_191311_create_user_roles_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'user_roles',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'role_id' => $this->integer(),
                'note' => $this->string(500),
                'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            ]
        );
        
        // creates index for column `user_id`
        $this->createIndex(
            'idx-user-roles-user-id',
            'user_roles',
            'user_id'
        );
    }

    public function down()
    {
        $this->dropTable('user_roles');
    }
}
