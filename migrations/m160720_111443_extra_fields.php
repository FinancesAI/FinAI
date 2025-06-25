<?php

use yii\db\Migration;

class m160720_111443_extra_fields extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `loan_extra` ADD  `car_owner_declared_address` VARCHAR( 300 ) NULL AFTER  `car_work_experience` ,
ADD  `credit_data` TEXT NULL AFTER  `car_owner_declared_address` ;")->execute();
    }

    public function down()
    {
        echo "m160720_111443_extra_fields cannot be reverted.\n";

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
