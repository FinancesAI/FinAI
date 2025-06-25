<?php

use yii\db\Migration;

class m160519_075439_changes_old extends Migration
{
    public function up()
    {
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'received'")->execute();
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'is_email'")->execute();
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'is_sms'")->execute();
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'is_call'")->execute();
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'is_courier_sent'")->execute();
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'is_received_vsaa'")->execute();
    	$this->db->createCommand("DELETE FROM `changes` WHERE `changes`.`attr` = 'is_received_statement'")->execute();
    }

    public function down()
    {
        echo "m160519_075439_changes_old cannot be reverted.\n";

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
