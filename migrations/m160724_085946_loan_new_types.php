<?php

use yii\db\Migration;

class m160724_085946_loan_new_types extends Migration
{
    public function up()
    {
$this->db->createCommand("CREATE TABLE IF NOT EXISTS `loan_guarantor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(10) unsigned NOT NULL,
  `full_name` varchar(256) DEFAULT NULL,
  `personal_code` varchar(256) DEFAULT NULL,
  `workplace` varchar(256) DEFAULT NULL,
  `workposition` varchar(256) DEFAULT NULL,
  `phone` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `income` double(8,2) DEFAULT NULL,
  `outcome` double(8,2) DEFAULT NULL,
  `bank_statement` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
$this->db->createCommand("ALTER TABLE `loan_guarantor`
  ADD CONSTRAINT `loan_guarantor_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
$this->db->createCommand("CREATE TABLE IF NOT EXISTS `loan_enterprise` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) unsigned NOT NULL,
  `name` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `nr` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `address_actual` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `address_domicile` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `bank_account_statement` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
$this->db->createCommand("ALTER TABLE `loan_enterprise`
  ADD CONSTRAINT `loan_enterprise_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
    }

    public function down()
    {
        echo "m160724_085946_loan_new_types cannot be reverted.\n";

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
