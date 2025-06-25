<?php

use yii\db\Migration;

class m160923_125713_ext_fields extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `person` ADD `family_status` TINYINT NULL AFTER `description`, ADD `education` TINYINT NULL AFTER `family_status`;")->execute();
    }

    public function down()
    {
        echo "m160923_125713_ext_fields cannot be reverted.\n";

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
