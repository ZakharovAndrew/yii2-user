<?php

use yii\db\Migration;

/**
 * Initializing roles
 */
class m240112_230011_add_column_subject_id_to_user_roles_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'user_roles',
                'subject_id', 
                $this->integer()->null()->after('note')
            );
    }

    public function down()
    {
        $this->dropColumn('user_roles', 'subject_id');
    }
}
