<?php

use yii\db\Migration;

class m160402_065002_field_view extends Migration
{
    public function up()
    {
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `field_view` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `field_id` int(10) unsigned NOT NULL,
		  `view` tinyint(1) NOT NULL,
		  `role` tinyint(1) DEFAULT '0',
		  `show` tinyint(1) DEFAULT '0',
		  `sort_order` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `field_id` (`field_id`),
		  KEY `view` (`view`,`role`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;")->execute();
		
		$this->db->createCommand("ALTER TABLE `field_view`
		ADD CONSTRAINT `field_view_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
		$this->dropColumn("field", "enable_table");
		$this->dropColumn("field", "enable_view");
		$this->dropColumn("field", "enable_form");
		$this->dropColumn("field", "role");
    }

    public function down()
    {
        echo "m160402_065002_field_view cannot be reverted.\n";

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
