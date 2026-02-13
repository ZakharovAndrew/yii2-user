<?php

use yii\db\Migration;

/**
 * Migration for adding friends count field
 */
class m250223_111117_add_friends_count_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'friends_count', $this->integer()->notNull()->defaultValue(0)->comment('Friends count'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'friends_count');
    }
}