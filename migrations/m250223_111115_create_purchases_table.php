<?php

use yii\db\Migration;

class m250223_111115_create_purchases_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('purchases', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'item_type' => $this->string(50)->notNull()->comment('Item type: avatar, wallpaper, achievement'),
            'item_id' => $this->integer()->notNull()->comment('Item ID in the corresponding table'),
            'price' => $this->integer()->notNull()->comment('Cost in coins'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        // Indexes
        $this->createIndex('idx-purchases-user_id', 'purchases', 'user_id');
    }

    public function safeDown()
    {
        $this->dropTable('purchases');
    }
}