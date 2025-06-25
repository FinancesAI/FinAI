<?php

use yii\db\Migration;

class m161125_094314_progress_bar extends Migration
{
    public function up()
    {
$this->db->createCommand("CREATE TABLE IF NOT EXISTS `loan_progerss` (
`id` int(10) unsigned NOT NULL,
  `loan_id` int(10) unsigned NOT NULL,
  `provider_id` tinyint(3) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `text` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `loan_progerss`
 ADD PRIMARY KEY (`id`), ADD KEY `loan_id` (`loan_id`);

ALTER TABLE `loan_progerss`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `loan_progerss`
ADD CONSTRAINT `loan_progerss_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();    	 
    }

    public function down()
    {
        echo "m161125_094314_progress_bar cannot be reverted.\n";

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
