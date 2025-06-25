<?php

use yii\db\Migration;

class m160712_145329_reminder_index extends Migration
{
    public function up()
    {
		$this->createIndex("reminder_time_index", "loan", "reminder_time");
    }

    public function down()
    {
        echo "m160712_145329_reminder_index cannot be reverted.\n";

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
