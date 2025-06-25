<?php

use yii\db\Migration;

class m160719_062323_aizdevums_api_2 extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `aizdevums_api` ADD  `contract_nr` VARCHAR( 250 ) NULL AFTER  `contract_id` ;")->execute();
    }

    public function down()
    {
        echo "m160719_062323_aizdevums_api_2 cannot be reverted.\n";

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
