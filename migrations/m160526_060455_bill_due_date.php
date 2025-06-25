<?php

use yii\db\Migration;

class m160526_060455_bill_due_date extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `bill` ADD  `due_time` INT UNSIGNED NULL AFTER  `close_time` ;")->execute();
    }

    public function down()
    {
        echo "m160526_060455_bill_due_date cannot be reverted.\n";

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
