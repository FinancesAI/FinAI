<?php

use yii\db\Migration;

class m160411_091746_ref_quey_lenght extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE loan DROP INDEX refferal;")->execute();
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `referral`  `referral` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;")->execute();
		$this->db->createCommand("ALTER TABLE  `loan` CHANGE  `query_string`  `query_string` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;")->execute();
    }

    public function down()
    {
        echo "m160411_091746_ref_quey_lenght cannot be reverted.\n";

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
