<?php

use yii\db\Migration;

/**
 * Initializing roles
 */
class m240112_230111_add_column_function_to_get_all_subjects_to_roles_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'roles',
                'function_to_get_all_subjects',
                $this->string(255)->null()->after('parameters')
            );
    }

    public function down()
    {
        $this->dropColumn('roles', 'function_to_get_all_subjects');
    }
}
