<?php

namespace app\modules\chat\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m220801_095650_add_chat_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%user}}', 'new_messages_email_time', $this->integer());
        $this->addColumn('{{%user}}', 'last_new_message_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%user}}', 'new_messages_email_time');
        $this->dropColumn('{{%user}}', 'last_new_message_id');
    }
}
