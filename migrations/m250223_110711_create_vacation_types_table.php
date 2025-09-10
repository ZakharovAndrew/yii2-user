<?php

use yii\db\Migration;
use ZakharovAndrew\user\Module;

/**
 * Class m250223_110711_create_vacation_types_table
 */
class m250223_110711_create_vacation_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%vacation_types}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text()->null(),
            'is_paid' => $this->boolean()->notNull()->defaultValue(true),
            'color' => $this->string(7)->notNull()->defaultValue('#007bff'),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'max_days_per_year' => $this->integer()->null()->comment('Maximum days allowed per year'),
            'min_days_per_request' => $this->integer()->null()->comment('Minimum days per request'),
            'max_days_per_request' => $this->integer()->null()->comment('Maximum days per request'),
            'requires_approval' => $this->boolean()->notNull()->defaultValue(true),
            'priority' => $this->integer()->notNull()->defaultValue(0)->comment('Sorting priority'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Добавляем индексы
        $this->createIndex('idx-vacation_types-is_active', '{{%vacation_types}}', 'is_active');
        $this->createIndex('idx-vacation_types-is_paid', '{{%vacation_types}}', 'is_paid');
        $this->createIndex('idx-vacation_types-priority', '{{%vacation_types}}', 'priority');

        // Вставляем популярные типы отпусков
        $this->insertDefaultVacationTypes();
    }

    /**
     * Insert default vacation types
     */
    private function insertDefaultVacationTypes()
    {
        $types = [
            [
                'name' => Module::t('Annual Paid Leave'),
                'description' => 'Basic paid annual vacation',
                'is_paid' => true,
                'color' => '#28a745',
                'max_days_per_year' => 28,
                'min_days_per_request' => 1,
                'max_days_per_request' => 14,
                'requires_approval' => true,
                'priority' => 10
            ],
            [
                'name' => Module::t('Unpaid Leave'),
                'description' => 'Leave without pay',
                'is_paid' => false,
                'color' => '#6c757d',
                'max_days_per_year' => 90,
                'min_days_per_request' => 1,
                'max_days_per_request' => 30,
                'requires_approval' => true,
                'priority' => 20
            ],
            [
                'name' => Module::t('Sick Leave'),
                'description' => 'Leave due to illness',
                'is_paid' => true,
                'color' => '#ffc107',
                'max_days_per_year' => 60,
                'min_days_per_request' => 1,
                'max_days_per_request' => 15,
                'requires_approval' => false,
                'priority' => 30
            ],
            [
                'name' => Module::t('Maternity Leave'),
                'description' => 'Maternity and pregnancy leave',
                'is_paid' => true,
                'color' => '#e83e8c',
                'max_days_per_year' => 140,
                'min_days_per_request' => 140,
                'max_days_per_request' => 140,
                'requires_approval' => false,
                'priority' => 40
            ],
            [
                'name' => Module::t('Study Leave'),
                'description' => 'Leave for educational purposes',
                'is_paid' => true,
                'color' => '#17a2b8',
                'max_days_per_year' => 30,
                'min_days_per_request' => 1,
                'max_days_per_request' => 10,
                'requires_approval' => true,
                'priority' => 50
            ],
            [
                'name' => Module::t('Childcare Leave'),
                'description' => 'Childcare leave up to 3 years',
                'is_paid' => true,
                'color' => '#fd7e14',
                'max_days_per_year' => 1095, // 3 years
                'min_days_per_request' => 30,
                'max_days_per_request' => 1095,
                'requires_approval' => false,
                'priority' => 60
            ],
            [
                'name' => Module::t('Creative Leave'),
                'description' => 'Leave for scientific or creative activities',
                'is_paid' => true,
                'color' => '#6f42c1',
                'max_days_per_year' => 30,
                'min_days_per_request' => 5,
                'max_days_per_request' => 15,
                'requires_approval' => true,
                'priority' => 70
            ],
            [
                'name' => Module::t('Administrative Leave'),
                'description' => 'Leave for personal circumstances',
                'is_paid' => false,
                'color' => '#20c997',
                'max_days_per_year' => 15,
                'min_days_per_request' => 1,
                'max_days_per_request' => 5,
                'requires_approval' => true,
                'priority' => 80
            ],
            [
                'name' => Module::t('Additional Leave'),
                'description' => 'Additional leave for special working conditions',
                'is_paid' => true,
                'color' => '#dc3545',
                'max_days_per_year' => 14,
                'min_days_per_request' => 1,
                'max_days_per_request' => 7,
                'requires_approval' => true,
                'priority' => 90
            ],
            [
                'name' => Module::t('Blood Donation Leave'),
                'description' => 'Leave for blood donors',
                'is_paid' => true,
                'color' => '#d63384',
                'max_days_per_year' => 5,
                'min_days_per_request' => 1,
                'max_days_per_request' => 2,
                'requires_approval' => false,
                'priority' => 100
            ]
        ];

        foreach ($types as $type) {
            $this->insert('{{%vacation_types}}', $type);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем внешние ключи из таблицы vacations (если они существуют)
        $tableSchema = Yii::$app->db->schema->getTableSchema('{{%vacations}}');
        if ($tableSchema !== null) {
            $foreignKeys = $tableSchema->foreignKeys;
            foreach ($foreignKeys as $fkName => $fkData) {
                if (isset($fkData['type_id'])) {
                    $this->dropForeignKey($fkName, '{{%vacations}}');
                }
            }
        }

        $this->dropTable('{{%vacation_types}}');
    }
}