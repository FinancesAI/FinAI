<?php

use yii\db\Migration;

class m160720_123036_mail_api_loan_id extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `loan_api` ADD  `loan_id` INT UNSIGNED NOT NULL AFTER  `type` ,
ADD INDEX (  `loan_id` ) ;")->execute();
    }

    public function down()
    {
        echo "m160720_123036_mail_api_loan_id cannot be reverted.\n";

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
