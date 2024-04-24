<?php

use yii\db\Migration;

/**
 * Initializing roles
 */
class m240111_215911_init_roles extends Migration
{
    public function up()
    {
        $this->insert('roles', [
            'title' => 'Admin',
            'code' => 'admin',
            'description' => 'Main role of admin',
        ]);

        $this->insert('user_roles', [
            'user_id' => 1,
            'role_id' => 1,
            'note' => 'init role',
        ]);
    }

    public function down()
    {
        $this->delete('roles', ['id' => 1]);
        $this->delete('user_roles', ['id' => 1]);
    }
}
