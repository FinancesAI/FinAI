<?php

use yii\db\Migration;

class m161124_064254_upd extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `loan` ADD `description_2` TEXT NULL AFTER `cid`, ADD `last_changed_field` VARCHAR(50) NULL AFTER `description_2`, ADD `rating` TINYINT(1) NULL AFTER `last_changed_field`;")->execute();
    }

    public function down()
    {
        echo "m161124_064254_upd cannot be reverted.\n";

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
    
	<field name="last_changed_field" type="string" indexed="true" stored="false" default="" />
	<field name="rating" type="string" indexed="true" stored="false" default="0" />

    */
}
