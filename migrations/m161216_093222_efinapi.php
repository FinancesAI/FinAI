<?php

use yii\db\Migration;

class m161216_093222_efinapi extends Migration
{
    public function up()
    {
    	$this->db->createCommand("CREATE TABLE IF NOT EXISTS `efinance_api` (
		    			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		    			`loan_id` int(10) unsigned NOT NULL,
		    			`contract_id` int(10) unsigned NOT NULL,
						PRIMARY KEY (`id`),
		    			UNIQUE KEY `contract_id` (`contract_id`),
		    			UNIQUE KEY `loan_id` (`loan_id`)
		    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
    	
    	$this->db->createCommand("ALTER TABLE `efinance_api` ADD CONSTRAINT `efinance_api_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
    }

    public function down()
    {
        echo "m161216_093222_efinapi cannot be reverted.\n";

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
