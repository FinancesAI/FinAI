<?php

use yii\db\Migration;

class m160524_094020_loan_unique extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `loan` ADD  `unique_id` VARCHAR( 250 ) NULL AFTER  `id` ;")->execute();
		$this->createIndex("unique_id", "loan", "unique_id", true);
    }

    public function down()
    {
        echo "m160524_094020_loan_unique cannot be reverted.\n";

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
