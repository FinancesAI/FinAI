<?php

namespace app\modules\chat\migrations;


use yii\db\Migration;

/**
 *
 */
class m220802_100501_create_chat_message_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%chat_message}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'from_user_id' => $this->integer()->unsigned()->notNull(),
            'to_user_id' => $this->integer()->unsigned()->notNull(),
            'text' => $this->string(1000)->null()->append('COLLATE utf8mb4_bin'),
            'is_new' => $this->boolean()->defaultValue(true),
            'is_deleted_by_sender' => $this->boolean()->defaultValue(false),
            'is_deleted_by_receiver' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('chat_message_from_user_idx', '{{%chat_message}}', 'from_user_id');
        $this->createIndex('chat_message_to_user_idx', '{{%chat_message}}', 'to_user_id');
        $this->createIndex('chat_message_is_new_idx', '{{%chat_message}}', 'is_new');
        $this->createIndex('chat_message_is_deleted_by_sender_idx', '{{%chat_message}}', 'is_deleted_by_sender');
        $this->createIndex('chat_message_is_deleted_by_receiver_idx', '{{%chat_message}}', 'is_deleted_by_receiver');

        $this->createIndex('chat_message_idx1_idx', '{{%chat_message}}', [
            'to_user_id', 'is_deleted_by_receiver',
        ]);

        $this->createIndex('chat_message_idx2_idx', '{{%chat_message}}', [
            'from_user_id', 'is_deleted_by_sender',
        ]);

        $this->createIndex('chat_message_idx3_idx', '{{%chat_message}}', [
            'from_user_id', 'to_user_id', 'is_deleted_by_receiver',
        ]);

        $this->createIndex('chat_message_idx4_idx', '{{%chat_message}}', [
            'from_user_id', 'to_user_id', 'is_deleted_by_sender',
        ]);

        $this->execute("SET foreign_key_checks = 0;");

        $this->addForeignKey('fk_chat_message_from_user',
            '{{%chat_message}}', 'from_user_id',
            '{{%user}}', 'id',
            'CASCADE', 'RESTRICT'
        );
        $this->addForeignKey('fk_chat_message_to_user',
            '{{%chat_message}}', 'to_user_id',
            '{{%user}}', 'id',
            'CASCADE', 'RESTRICT'
        );

        $this->execute("SET foreign_key_checks = 1;");
    }

    public function down()
    {
        $this->dropForeignKey('fk_chat_message_from_user', '{{%chat_message}}');
        $this->dropForeignKey('fk_chat_message_to_user', '{{%chat_message}}');

        $this->dropTable('{{%chat_message}}');
    }
}
