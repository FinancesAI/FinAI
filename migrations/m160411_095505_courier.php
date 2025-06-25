<?php

use yii\db\Migration;

class m160411_095505_courier extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `loan` ADD  `is_courier_sent` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `is_call` ;")->execute();
    }

    public function down()
    {
        echo "m160411_095505_courier cannot be reverted.\n";

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
