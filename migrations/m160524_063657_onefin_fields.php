<?php

use yii\db\Migration;

class m160524_063657_onefin_fields extends Migration
{
    public function up()
    {
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `loan_extra` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `loan_id` int(10) unsigned NOT NULL,
		  `property_address` varchar(256) DEFAULT NULL,
		  `car_description` varchar(512) DEFAULT NULL,
		  `car_phone` varchar(100) DEFAULT NULL,
		  `car_owner` varchar(256) DEFAULT NULL,
		  `car_owner_address` varchar(256) DEFAULT NULL,
		  `car_workplace` varchar(256) DEFAULT NULL,
		  `car_position` varchar(256) DEFAULT NULL,
		  `car_work_experience` varchar(256) DEFAULT NULL,
		  `vsaa_statement` varchar(512) DEFAULT NULL,
		  `bank_account_statement` varchar(512) DEFAULT NULL,
		  `api_status_description` varchar(512) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `loan_id` (`loan_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();

		$this->db->createCommand("ALTER TABLE `loan_extra`
		  ADD CONSTRAINT `loan_extra_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
  
 		$this->db->createCommand("ALTER TABLE  `person` ADD  `gender` CHAR( 1 ) NULL AFTER  `surname` ;")->execute();
    }

    public function down()
    {
        echo "m160524_063657_onefin_fields cannot be reverted.\n";

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
