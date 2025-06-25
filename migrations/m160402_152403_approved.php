<?php

use yii\db\Migration;

class m160402_152403_approved extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `loan` ADD  `approved` TINYINT( 2 ) NULL DEFAULT '0' AFTER  `term` ;")->execute();
    	$this->db->createCommand("ALTER TABLE  `loan` ADD  `first_payment` INT(10) NULL DEFAULT '0' AFTER  `term` ;")->execute();
    }

    public function down()
    {
        echo "m160402_152403_approved cannot be reverted.\n";

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
