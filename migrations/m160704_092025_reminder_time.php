<?php

use yii\db\Migration;

class m160704_092025_reminder_time extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `loan` ADD  `reminder_time` INT( 10 ) UNSIGNED NULL AFTER  `close_time` ;")->execute();
    }

    public function down()
    {
        echo "m160704_092025_reminder_time cannot be reverted.\n";

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
