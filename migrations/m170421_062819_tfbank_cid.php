<?php

use yii\db\Migration;

class m170421_062819_tfbank_cid extends Migration
{
    public function up()
    {
    	$this->db->createCommand("CREATE TABLE IF NOT EXISTS `tfbank_api` (
		    			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		    			`loan_id` int(10) unsigned NOT NULL,
		    			`contract_id` int(10) unsigned NOT NULL,
						PRIMARY KEY (`id`),
		    			UNIQUE KEY `contract_id` (`contract_id`),
		    			UNIQUE KEY `loan_id` (`loan_id`)
		    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
    	
    	$this->db->createCommand("ALTER TABLE `tfbank_api` ADD CONSTRAINT `tfbank_api_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
    }

    public function down()
    {
        echo "m170421_062819_tfbank_cid cannot be reverted.\n";

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
