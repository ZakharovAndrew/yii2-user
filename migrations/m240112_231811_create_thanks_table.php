<?php

use yii\db\Migration;

/**
 * Handles the creation of table `thanks`.
 */
class m240112_231811_create_thanks_table extends Migration
{
    public function up()
    {
        $this->createTable('thanks', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->Null(),
            'text' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-thanks-user_id',
            'thanks',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-thanks-author_id',
            'thanks',
            'author_id',
            'user',
            'id',
            'SET NULL'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-thanks-user_id', 'thanks');
        $this->dropForeignKey('fk-thanks-author_id', 'thanks');
        $this->dropTable('thanks');
    }
}