<?php

namespace app\modules\chat\migrations;

use yii\db\Migration;

/**
 *
 */
class m220802_101001_create_chat_message_attachment_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%chat_message_attachment}}', [
            'id' => $this->primaryKey(),
            'message_id' => $this->bigInteger(20)->notNull()->unsigned(),
            'type' => $this->string(32)->notNull(),
            'data' => $this->text(),
        ]);

        $this->createIndex('message_idx', '{{%chat_message_attachment}}', ['message_id']);
        $this->createIndex('type', '{{%chat_message_attachment}}', ['type']);

        $this->execute("SET foreign_key_checks = 0;");

        $this->addForeignKey('fk_message_attachment_message',
            '{{%chat_message_attachment}}', 'message_id',
            '{{%chat_message}}', 'id',
            'CASCADE', 'RESTRICT'
        );

        $this->execute("SET foreign_key_checks = 1;");
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_message_attachment_message', '{{%chat_message_attachment}}');

        $this->dropTable('{{%chat_message_attachment}}');
    }
}
