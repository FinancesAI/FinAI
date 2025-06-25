<?php

use yii\db\Migration;

class m160720_122217_loan_api extends Migration
{
    public function up()
    {
$this->db->createCommand("CREATE TABLE IF NOT EXISTS `loan_api` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_hash` varchar(250) CHARACTER SET utf8 NOT NULL,
  `password` varchar(250) CHARACTER SET utf8 NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_hash` (`url_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;")->execute();
$this->db->createCommand("ALTER TABLE  `mail` ADD  `api_type` TINYINT NULL AFTER  `in_menu` ;")->execute();
    }

    public function down()
    {
        echo "m160720_122217_loan_api cannot be reverted.\n";

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
