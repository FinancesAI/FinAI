<?php

use yii\db\Migration;

class m170301_100830_progress_bar_amount extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `loan_progerss` ADD `amount` VARCHAR(250) NULL DEFAULT NULL AFTER `text`;")->execute();
$this->db->createCommand("ALTER TABLE `mail` ADD `order_by` INT UNSIGNED NULL AFTER `api_type`;")->execute();
    }

    public function down()
    {
        echo "m170301_100830_progress_bar_amount cannot be reverted.\n";

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
