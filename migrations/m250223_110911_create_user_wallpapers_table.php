<?php

use yii\db\Migration;

class m250223_110911_create_user_wallpapers_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_wallpapers', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'image_url' => $this->string(500)->notNull(),
            'css_settings' => $this->text(),
            'mobile_css_settings' => $this->text(),
            'position' => $this->integer()->defaultValue(0),
            'status' => $this->smallInteger()->defaultValue(1),
            'roles' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-wallpaper-status', 'user_wallpapers', 'status');
        $this->createIndex('idx-wallpaper-position', 'user_wallpapers', 'position');
    }

    public function safeDown()
    {
        $this->dropTable('user_wallpapers');
    }
}
