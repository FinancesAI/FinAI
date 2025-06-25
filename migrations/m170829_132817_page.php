<?php

use yii\db\Migration;

class m170829_132817_page extends Migration
{
    public function up()
    {
    	$this->db->createCommand("CREATE TABLE IF NOT EXISTS `page` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(250) CHARACTER SET utf8 NOT NULL,
		`content` text CHARACTER SET utf8 NOT NULL,
		`in_menu` tinyint(4) DEFAULT '0',
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
    }

    public function down()
    {
        echo "m170829_132817_page cannot be reverted.\n";

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
