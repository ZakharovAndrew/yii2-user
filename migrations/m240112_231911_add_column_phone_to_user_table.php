<?php

use yii\db\Migration;


class m240112_231911_add_column_phone_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'phone', $this->string(20));
    }

    public function down()
    {
        $this->dropColumn('users', 'phone');
    }
}
