<?php

namespace app\modules\chat\migrations;

use Yii;
use yii\db\Exception;
use yii\db\Migration;

/**
 * Class m220801_102420_add_chat_value_to_setting_table
 */
class m220801_102420_add_chat_value_to_setting_table extends Migration
{
    /**
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $this->db->createCommand(
            "INSERT INTO `setting` (`name`, `value`) VALUES ('receive_email_on_new_messages', '1');"
        )->execute();

        Yii::$app->cache->flush();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function down()
    {
        $this->db->createCommand(
            "DELETE FROM `setting` WHERE `name` = 'receive_email_on_new_messages';"
        )->execute();

        Yii::$app->cache->flush();
    }
}
