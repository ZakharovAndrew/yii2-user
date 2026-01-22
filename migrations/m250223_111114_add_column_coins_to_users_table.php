<?php

use yii\db\Migration;

/**
 * Add column `coins` to users table
 */
class m250223_111114_add_column_coins_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'coins', $this->bigInteger()->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('users', 'coins');
    }
}
