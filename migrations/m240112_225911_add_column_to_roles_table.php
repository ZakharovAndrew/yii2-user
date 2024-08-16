<?php

use yii\db\Migration;

/**
 * Add column `parameters` to roles table
 */
class m240112_225911_add_column_to_roles_table extends Migration
{
    public function up()
    {
        $this->addColumn('roles', 'parameters', 'text');
    }

    public function down()
    {
        $this->dropColumn('roles', 'parameters');
    }
}
