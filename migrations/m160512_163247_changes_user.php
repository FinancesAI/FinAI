<?php

use yii\db\Migration;

class m160512_163247_changes_user extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `changes` CHANGE  `user_id`  `user_id` INT( 10 ) UNSIGNED NULL ;")->execute();
    }

    public function down()
    {
        echo "m160512_163247_changes_user cannot be reverted.\n";

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
