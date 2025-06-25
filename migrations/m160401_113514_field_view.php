<?php

use yii\db\Migration;

class m160401_113514_field_view extends Migration
{
    public function up()
    {
    	$this->db->createCommand("ALTER TABLE  `field` ADD  `enable_view` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `enable_table` ;")->execute();
    }

    public function down()
    {
        echo "m160401_113514_field_view cannot be reverted.\n";

        return false;
    }
}
