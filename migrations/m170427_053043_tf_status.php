<?php

use yii\db\Migration;

class m170427_053043_tf_status extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE `tfbank_api` ADD `status` VARCHAR(50) NULL AFTER `contract_id`;")->execute();
    }

    public function down()
    {
        echo "m170427_053043_tf_status cannot be reverted.\n";

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
