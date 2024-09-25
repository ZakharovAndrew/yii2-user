<?php

use yii\db\Migration;

/**
 * Initializing roles
 */
class m240112_231711_add_column_telegam_code_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'telegram_code', $this->string(255)->notNull());
    }

    public function down()
    {
        $this->dropColumn('users', 'telegram_code');
    }
}
