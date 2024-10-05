<?php

use yii\db\Migration;


class m240112_232111_add_column_created_by_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'created_by', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('users', 'created_by');
    }
}
