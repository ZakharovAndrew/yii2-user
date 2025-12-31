<?php

use yii\db\Migration;

/**
 * Add column `email_verification_code` to users table
 */
class m250223_111112_add_column_email_verification_code_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'email_verification_code', $this->string(10)->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('users', 'email_verification_code');
    }
}
