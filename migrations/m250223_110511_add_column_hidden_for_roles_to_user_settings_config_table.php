<?php

use yii\db\Migration;

/**
 * Initializing roles
 */
class m250223_110511_add_column_hidden_for_roles_to_user_settings_config_table extends Migration
{
    public function up()
    {
        $this->addColumn('user_settings_config', 'hidden_for_roles', $this->string(500)->notNull());
    }

    public function down()
    {
        $this->dropColumn('user_settings_config', 'hidden_for_roles');
    }
}
