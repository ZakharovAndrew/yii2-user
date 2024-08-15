<?php

use yii\db\Migration;

/**
 * Initializing roles
 */
class m240112_231611_add_column_telegam_id_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'telegram_id', $this->bigInteger()->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('users', 'telegram_id');
    }
}
