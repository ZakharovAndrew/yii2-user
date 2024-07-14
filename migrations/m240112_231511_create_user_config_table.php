<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_settings`.
 */
class m240112_231511_create_user_config_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'user_settings',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'setting_config_id' => $this->integer(),
                'values' => $this->string(500),
            ]
        );
        
        // add foreign key for table `user_settings_config`
        $this->addForeignKey(
            'fk-user-settings-setting_config_id',
            'user_settings',
            'setting_config_id',
            'user_settings_config',
            'id',
            'CASCADE'
        );
        
        // creates index for column `user_id`
        $this->createIndex(
            'idx-user-settings-user_id',
            'user_settings',
            'user_id'
        );
    }

    public function down()
    {
        $this->dropTable('user_settings_config');
    }
}
