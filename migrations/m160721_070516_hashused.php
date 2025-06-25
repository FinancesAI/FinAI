<?php

use yii\db\Migration;

class m160721_070516_hashused extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `loan_api` ADD  `used` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `loan_id` ;")->execute();
    }

    public function down()
    {
        echo "m160721_070516_hashused cannot be reverted.\n";

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
