<?php

use yii\db\Migration;

class m160724_075448_loan_extra_new extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `loan_extra` ADD  `document_type` VARCHAR( 256 ) NULL AFTER  `credit_data` ,
ADD  `document_nr` VARCHAR( 256 ) NULL AFTER  `document_type` ,
ADD  `document_expire` VARCHAR( 256 ) NULL AFTER  `document_nr` ,
ADD  `bank_account_nr` VARCHAR( 256 ) NULL AFTER  `document_expire` ,
ADD  `vsaa_statement_ep52` VARCHAR( 512 ) NULL AFTER  `bank_account_nr` ;")->execute();
    }

    public function down()
    {
        echo "m160724_075448_loan_extra_new cannot be reverted.\n";

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
