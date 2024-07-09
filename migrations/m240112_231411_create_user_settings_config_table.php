<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_settings_config`.
 */
class m240112_201411_create_user_settings_config_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'user_settings_config',
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(255)->notNull(),
                'code' => $this->string(255)->notNull(),
                'type' => $this->integer(),
                'access_level' => $this->integer(),
                'values' => $this->text,
            ]
        );
        
        // creates index for column `user_id`
        $this->createIndex(
            'idx-user-settings-config-code',
            'user_settings_config',
            'code'
        );
    }

    public function down()
    {
        $this->dropTable('user_settings_config');
    }
}
