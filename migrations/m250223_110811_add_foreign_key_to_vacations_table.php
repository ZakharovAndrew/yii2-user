<?php

use yii\db\Migration;

/**
 * Class m250223_110811_add_foreign_key_to_vacations_table
 */
class m250223_110811_add_foreign_key_to_vacations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Проверяем существование таблицы vacations
        $tableExists = Yii::$app->db->getTableSchema('{{%vacations}}') !== null;
        
        if ($tableExists) {
            // Добавляем внешний ключ для type_id
            $this->addForeignKey(
                'fk-vacations-type_id',
                '{{%vacations}}',
                'type_id',
                '{{%vacation_types}}',
                'id',
                'RESTRICT',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-vacations-type_id', '{{%vacations}}');
    }
}