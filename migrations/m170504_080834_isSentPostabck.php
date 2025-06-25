<?php

use yii\db\Migration;

class m170504_080834_isSentPostabck extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `loan` ADD `is_tc_sent` TINYINT(1) NULL DEFAULT '0' AFTER `ceo`;")->execute();
    }

    public function down()
    {
        echo "m170504_080834_isSentPostabck cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
