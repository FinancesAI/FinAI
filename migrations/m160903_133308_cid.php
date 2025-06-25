<?php

use yii\db\Migration;

class m160903_133308_cid extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `loan` ADD `cid` VARCHAR(250) NULL AFTER `need_reindex`;")->execute();
    }

    public function down()
    {
        echo "m160903_133308_cid cannot be reverted.\n";

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
