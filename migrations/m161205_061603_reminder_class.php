<?php

use yii\db\Migration;

class m161205_061603_reminder_class extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `loan` ADD `reminder_class` TINYINT(1) NULL DEFAULT '0' AFTER `rating`;")->execute();
    }

    public function down()
    {
        echo "m161205_061603_reminder_class cannot be reverted.\n";

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
