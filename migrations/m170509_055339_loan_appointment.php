<?php

use yii\db\Migration;

class m170509_055339_loan_appointment extends Migration
{
    public function up()
    {
$this->db->createCommand("CREATE TABLE `loan_appointment` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `loan_appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

ALTER TABLE `loan_appointment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `loan_appointment`
  ADD CONSTRAINT `loan_appointment_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();
    }

    public function down()
    {
        echo "m170509_055339_loan_appointment cannot be reverted.\n";

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
