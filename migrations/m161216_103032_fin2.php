<?php

use yii\db\Migration;

class m161216_103032_fin2 extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `efinance_api` ADD `is_updated` TINYINT UNSIGNED NULL DEFAULT '0' AFTER `contract_id`;")->execute();
    }

    public function down()
    {
        echo "m161216_103032_fin2 cannot be reverted.\n";

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
