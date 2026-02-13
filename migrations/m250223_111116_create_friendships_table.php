<?php

use yii\db\Migration;

/**
 * Class m250223_111116_create_friendships_table
 */
class m250223_111116_create_friendships_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create friendships table
        $this->createTable('{{%friendships}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('User who sent the request'),
            'friend_id' => $this->integer()->notNull()->comment('User who received the request'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Status: 0 - pending, 1 - accepted, 2 - rejected, 3 - blocked'),
            'responded_at' => $this->timestamp()->null()->comment('Response time'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        // Optimized indexes
        // Composite unique index to prevent duplicate friendships (in any direction)
        $this->createIndex('idx-friendships-user-friend-unique', '{{%friendships}}', 
            ['user_id', 'friend_id'], true);
        
        // Index for searching by friend_id (for received requests)
        $this->createIndex('idx-friendships-friend_id-status', '{{%friendships}}', 
            ['friend_id', 'status']);
        
        // Index for searching by user_id (for sent requests)
        $this->createIndex('idx-friendships-user_id-status', '{{%friendships}}', 
            ['user_id', 'status']);
        
        // Index for responded_at (for cleanup operations)
        $this->createIndex('idx-friendships-responded_at', '{{%friendships}}', 
            'responded_at');

        // Foreign keys
        $this->addForeignKey(
            'fk-friendships-user_id',
            '{{%friendships}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-friendships-friend_id',
            '{{%friendships}}',
            'friend_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Table comment
        $this->addCommentOnTable('{{%friendships}}', 'Friendships between users');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-friendships-user_id', '{{%friendships}}');
        $this->dropForeignKey('fk-friendships-friend_id', '{{%friendships}}');
        
        $this->dropIndex('idx-friendships-user-friend-unique', '{{%friendships}}');
        $this->dropIndex('idx-friendships-friend_id-status', '{{%friendships}}');
        $this->dropIndex('idx-friendships-user_id-status', '{{%friendships}}');
        $this->dropIndex('idx-friendships-responded_at', '{{%friendships}}');
        
        $this->dropTable('{{%friendships}}');
    }
}