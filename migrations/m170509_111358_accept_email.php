<?php

use yii\db\Migration;

class m170509_111358_accept_email extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `person` ADD `accept_email` TINYINT UNSIGNED NULL DEFAULT '1' AFTER `personal_code`;")->execute();
    }

    public function down()
    {
        echo "m170509_111358_accept_email cannot be reverted.\n";

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
