<?php

use yii\db\Migration;

class m160720_111431_mail extends Migration
{
    public function up()
    {
$this->db->createCommand("CREATE TABLE IF NOT EXISTS `mail` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(250) CHARACTER SET utf8 NOT NULL,
		`menu_title` varchar(250) CHARACTER SET utf8 NOT NULL,
		`content` text CHARACTER SET utf8 NOT NULL,
		`custom_id` int(10) unsigned NOT NULL,
		`in_menu` tinyint(4) DEFAULT '0',
		PRIMARY KEY (`id`),
		KEY `custom_id` (`custom_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;")->execute();

    }

    public function down()
    {
        echo "m160720_111431_mail cannot be reverted.\n";

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
