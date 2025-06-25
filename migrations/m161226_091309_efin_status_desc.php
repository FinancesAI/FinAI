<?php

use yii\db\Migration;

class m161226_091309_efin_status_desc extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `efinance_api` ADD `status_desc` TEXT NULL AFTER `is_updated`;")->execute();
    }

    public function down()
    {
        echo "m161226_091309_efin_status_desc cannot be reverted.\n";

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
