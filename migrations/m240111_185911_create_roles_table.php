<?php

use yii\db\Migration;

/**
 * Handles the creation of table `roles`.
 */
class m240111_185911_create_roles_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'roles',
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(255)->notNull(),
                'code' => $this->string(255)->notNull(),
                'description' => $this->text(),
                'created_at' => $this->dateTime()->defaultValue( new \yii\db\Expression('NOW()') ),
            ]
        );
        
        // creates index for column `code`
        $this->createIndex(
            'idx-roles-code',
            'roles',
            'code'
        );
    }

    public function down()
    {
        $this->dropTable('roles');
    }
}
