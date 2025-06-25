<?php

use yii\db\Migration;

class m160401_114543_loan_checkbox extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `loan` ADD  `is_received_statement` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `is_call` ;")->execute();
    	$this->db->createCommand("ALTER TABLE  `loan` ADD  `is_received_vsaa` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `is_call` ;")->execute();
    }

    public function down()
    {
        echo "m160401_114543_loan_checkbox cannot be reverted.\n";

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
