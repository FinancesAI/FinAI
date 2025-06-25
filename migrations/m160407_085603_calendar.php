<?php

use yii\db\Migration;

class m160407_085603_calendar extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `user_calendar` CHANGE  `embed`  `title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;")->execute();
    	$this->db->createCommand("ALTER TABLE  `user_calendar` ADD  `start` INT( 10 ) NULL AFTER  `title` ,
ADD  `end` INT( 10 ) NULL AFTER  `start` ;")->execute();
    	 
    }

    public function down()
    {
        echo "m160407_085603_calendar cannot be reverted.\n";

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
