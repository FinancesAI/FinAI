<?php

use yii\db\Migration;

class m161226_101647_loan_gur_fi extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `loan_guarantor` ADD `work_experience` INT NULL AFTER `workposition`, ADD `address` VARCHAR(250) NULL AFTER `work_experience`, ADD `postcode` VARCHAR(8) NULL AFTER `address`;")->execute();
    }

    public function down()
    {
        echo "m161226_101647_loan_gur_fi cannot be reverted.\n";

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
