<?php

use yii\db\Migration;
use ZakharovAndrew\user\models\UserSettingsConfig;

class m250223_110411_create_wallpaper_setting extends Migration
{
    public function up()
    {
        $this->insert('user_settings_config', [
            'title' => 'Useer wallpaper ID',
            'code' => 'user_wallpaper_id',
            'type' => UserSettingsConfig::TYPE_INT,
            'access_level' => UserSettingsConfig::CHANGE_SYSTEM_ONLY,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->delete('notifications', ['code' => 'user_wallpaper_id']);
    }
}
