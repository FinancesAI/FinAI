<?php

use yii\db\Migration;

class m160610_065138_inbank_api_nr extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `loan_extra` ADD  `inbank_contract_id` VARCHAR( 250 ) NULL AFTER  `loan_id` ;")->execute();
    }

    public function down()
    {
        echo "m160610_065138_inbank_api_nr cannot be reverted.\n";

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
