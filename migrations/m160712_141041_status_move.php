<?php

use yii\db\Migration;

class m160712_141041_status_move extends Migration
{
    public function up()
    {
    	$this->db->createCommand("UPDATE `changes` set attr_to=1 WHERE `attr` = 'status' AND attr_to = 2;
    	UPDATE `changes` set attr_from=1 WHERE `attr` = 'status' AND attr_from = 2;
    	UPDATE `changes` set attr_to=1 WHERE `attr` = 'status' AND attr_to = 6;
    	UPDATE `changes` set attr_from=1 WHERE `attr` = 'status' AND attr_from = 6;")->execute();
    }

    public function down()
    {
        echo "m160712_141041_status_move cannot be reverted.\n";

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
