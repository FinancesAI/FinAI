<?php

use yii\db\Migration;

class m160518_091035_remove_fields extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE `loan`
		  DROP `received`,
		  DROP `is_email`,
		  DROP `is_sms`,
		  DROP `is_call`,
		  DROP `is_courier_sent`,
		  DROP `is_received_vsaa`,
		  DROP `is_received_statement`;")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'received'")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'is_email'")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'is_sms'")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'is_call'")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'is_courier_sent'")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'is_received_vsaa'")->execute();
		  $this->db->createCommand("DELETE FROM `field` WHERE `field`.`name` = 'is_received_statement'")->execute();
    }

    public function down()
    {
        echo "m160518_091035_remove_fields cannot be reverted.\n";

        return false;
    }
}
