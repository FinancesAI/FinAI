<?php

use yii\db\Migration;

class m160402_161311_deal_product extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `loan` ADD  `deal_product` SMALLINT UNSIGNED NOT NULL AFTER  `deal_stage` ;")->execute();
    }

    public function down()
    {
        echo "m160402_161311_deal_product cannot be reverted.\n";

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
