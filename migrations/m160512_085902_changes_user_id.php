<?php

use yii\db\Migration;

class m160512_085902_changes_user_id extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `changes` ADD  `user_id` INT UNSIGNED NOT NULL AFTER  `type_id` , ADD INDEX (  `user_id` ) ;")->execute();
    }

    public function down()
    {
        echo "m160512_085902_changes_user_id cannot be reverted.\n";

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
