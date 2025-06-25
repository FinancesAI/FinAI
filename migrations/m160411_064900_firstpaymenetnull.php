<?php

use yii\db\Migration;

class m160411_064900_firstpaymenetnull extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `first_payment`  `first_payment` DOUBLE( 8, 2 ) NULL DEFAULT  '0';")->execute();
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `deal_product`  `deal_product` SMALLINT( 5 ) UNSIGNED NULL DEFAULT  '0';")->execute();
    }

    public function down()
    {
        echo "m160411_064900_firstpaymenetnull cannot be reverted.\n";

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
