<?php

use yii\db\Migration;

class m160408_095508_changes extends Migration
{
    public function up()
    {
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `attr` varchar(250) NOT NULL,
  `attr_from` text,
  `attr_to` text,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
    }

    public function down()
    {
        echo "m160408_095508_changes cannot be reverted.\n";

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
