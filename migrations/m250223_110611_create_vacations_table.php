<?php

use yii\db\Migration;

/**
 * Class m250223_110611_create_vacations_table
 */
class m250223_110611_create_vacations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vacations}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'days_count' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'comment' => $this->text(),
            'created_by' => $this->integer(),
            'approved_by' => $this->integer(),
            'approved_at' => $this->timestamp()->null(),
            'rejected_by' => $this->integer(),
            'rejected_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-vacations-user_id', '{{%vacations}}', 'user_id');
        $this->createIndex('idx-vacations-type_id', '{{%vacations}}', 'type_id');
        $this->createIndex('idx-vacations-status', '{{%vacations}}', 'status');
        $this->createIndex('idx-vacations-dates', '{{%vacations}}', ['start_date', 'end_date']);
        $this->createIndex('idx-vacations-approved_at', '{{%vacations}}', 'approved_at');
        $this->createIndex('idx-vacations-rejected_at', '{{%vacations}}', 'rejected_at');

        $this->addForeignKey(
            'fk-vacations-user_id',
            '{{%vacations}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-vacations-user_id', '{{%vacations}}');
        $this->dropTable('{{%vacations}}');
    }
}