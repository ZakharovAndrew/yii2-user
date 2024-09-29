<?php

use yii\db\Migration;

/**
 * Handles the creation of table `birthday_greeting`.
 */
class m240112_232011_create_birthday_greeting_table extends Migration
{
    public function up()
    {
        $this->createTable('birthday_greeting', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'author_id' => $this->integer()->Null(),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'is_read' => $this->boolean()->defaultValue(false),
        ]);

        $this->createIndex(
            'idx-birthday_greeting-user_id',
            'birthday_greeting',
            'user_id'
        );

        $this->addForeignKey(
            'fk-birthday_greeting-user_id',
            'birthday_greeting',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey(
            'fk-birthday_greeting-user_id',
            'birthday_greeting'
        );

        $this->dropIndex(
            'idx-birthday_greeting-user_id',
            'birthday_greeting'
        );

        $this->dropTable('birthday_greeting');
    }
}
