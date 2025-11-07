<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story}}`.
 */
class m251107_151016_create_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story}}', [
            'id' => $this->primaryKey(),
            'author_name' => $this->string(15)->null(),
            'email' => $this->string(191)->null(),
            'body' => $this->text()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'user_agent' => $this->string(255)->null(),
            'created_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->null(),
            'deleted_ip' => $this->string(45)->null(),
            'manage_token' => $this->string(64)->notNull(),
        ]);

        $this->createIndex('idx_story_ip', '{{%story}}', 'ip');
        $this->createIndex('idx_story_created_at', '{{%story}}', 'created_at');
        $this->createIndex('idx_story_deleted_at', '{{%story}}', 'deleted_at');
        $this->createIndex('idx_story_manage_token', '{{%story}}', 'manage_token', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story}}');
    }
}
