<?php

use yii\db\Migration;

class m160517_122412_actions extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `loan` ADD  `actions` VARCHAR( 200 ) NULL AFTER  `ip_ountry` ;")->execute();
    }

    public function down()
    {
        echo "m160517_122412_actions cannot be reverted.\n";

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
