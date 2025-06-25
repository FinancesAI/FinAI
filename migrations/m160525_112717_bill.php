<?php

use yii\db\Migration;

class m160525_112717_bill extends Migration
{
    public function up()
    {
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `bill` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `loan_id` int(10) unsigned NOT NULL,
		  `amount` double(8,2) NOT NULL,
		  `status` tinyint(1) DEFAULT '0',
		  `type` tinyint(1) NOT NULL,
		  `create_time` int(10) unsigned DEFAULT NULL,
		  `update_time` int(10) unsigned DEFAULT NULL,
		  `close_time` int(10) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `status` (`status`),
		  UNIQUE KEY `loan_id` (`loan_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
		
		$this->db->createCommand("ALTER TABLE `bill`
		  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
    }

    public function down()
    {
        echo "m160525_112717_bill cannot be reverted.\n";

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
