<?php

use yii\db\Migration;

class m250223_110311_add_fields_to_notifications extends Migration
{
    public function safeUp()
    {
        $this->addColumn('notifications', 'code_name', $this->string(255)->null()->after('name'));
        $this->addColumn('notifications', 'function_to_call', $this->string(255)->null()->after('code_name'));
    }

    public function safeDown()
    {
        $this->dropColumn('notifications', 'function_to_call');
        $this->dropColumn('notifications', 'code_name');
    }
}