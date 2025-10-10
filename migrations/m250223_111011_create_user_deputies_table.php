<?php

use yii\db\Migration;

/**
 * Class m250223_111011_create_user_deputies_table
 */
class m250223_111011_create_user_deputies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_deputies', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'deputy_user_id' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->datetime()->notNull(),
            'valid_from' => $this->date()->notNull(),
            'valid_to' => $this->date()->null(),
            'is_active' => $this->smallInteger()->defaultValue(1),
        ]);

        // Add foreign keys
        $this->addForeignKey(
            'fk-user_deputies-user_id',
            'user_deputies',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_deputies-deputy_user_id',
            'user_deputies',
            'deputy_user_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_deputies-created_by',
            'user_deputies',
            'created_by',
            'users',
            'id',
            'CASCADE'
        );

        // Add indexes
        $this->createIndex('idx-user_deputies-user_id', 'user_deputies', 'user_id');
        $this->createIndex('idx-user_deputies-deputy_user_id', 'user_deputies', 'deputy_user_id');
        $this->createIndex('idx-user_deputies-valid_dates', 'user_deputies', ['valid_from', 'valid_to']);
        $this->createIndex('idx-user_deputies-active', 'user_deputies', ['is_active']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_deputies-user_id', 'user_deputies');
        $this->dropForeignKey('fk-user_deputies-deputy_user_id', 'user_deputies');
        $this->dropForeignKey('fk-user_deputies-created_by', 'user_deputies');
        $this->dropTable('user_deputies');
    }
}