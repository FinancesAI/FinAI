<?php

use yii\db\Migration;

class m160712_124918_status_migrate extends Migration
{
    public function up()
    {
		$this->db->createCommand("UPDATE loan set status=1 where status=2;UPDATE loan set status=1 where status=6;")->execute();
    }

    public function down()
    {
        echo "m160712_124918_status_migrate cannot be reverted.\n";

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
