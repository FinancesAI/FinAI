<?php

use yii\db\Migration;

class m160406_081940_int_to_dubble extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `amount`  `amount` DOUBLE( 8, 2 ) NOT NULL ;")->execute();
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `first_payment`  `first_payment` DOUBLE( 8, 2 ) NOT NULL ;")->execute();
		$this->db->createCommand("ALTER TABLE  `person` CHANGE  `income`  `income` DOUBLE( 8, 2 ) NOT NULL ;")->execute();
		$this->db->createCommand("ALTER TABLE  `person` CHANGE  `outcome`  `outcome` DOUBLE( 8, 2 ) NOT NULL ;")->execute();
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `refferal`  `referral` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;")->execute();
    }

    public function down()
    {
        echo "m160406_081940_int_to_dubble cannot be reverted.\n";

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
