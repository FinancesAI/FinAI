<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%message_template}}`.
 */
class m220620_193209_create_message_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%message_template}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string()->notNull(),
            'content' => $this->text(),
        ]);

        $this->createIndex(
            'idx-message_template-user_id',
            '{{%message_template}}',
            'user_id'
        );

        $this->execute("SET foreign_key_checks = 0;");

        $this->addForeignKey(
            'fk-message_template-user_id',
            '{{%message_template}}',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->execute("SET foreign_key_checks = 1;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->execute("SET foreign_key_checks = 0;");

        $this->dropForeignKey(
            'fk-message_template-user_id',
            '{{%message_template}}'
        );

        $this->execute("SET foreign_key_checks = 1;");

        $this->dropIndex(
            'idx-message_template-user_id',
            '{{%message_template}}'
        );

        $this->dropTable('{{%message_template}}');
    }
}
