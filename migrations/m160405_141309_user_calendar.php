<?php

use yii\db\Migration;

class m160405_141309_user_calendar extends Migration
{
    public function up()
    {
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `user_calendar` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` int(10) unsigned NOT NULL,
		  `embed` text NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
		$this->db->createCommand("ALTER TABLE `user_calendar`
  		ADD CONSTRAINT `user_calendar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);")->execute();
    }

    public function down()
    {
        echo "m160405_141309_user_calendar cannot be reverted.\n";

        return false;
    }
}
