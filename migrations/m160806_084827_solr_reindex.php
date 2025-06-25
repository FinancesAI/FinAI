<?php

use yii\db\Migration;

class m160806_084827_solr_reindex extends Migration
{
    public function up()
    {
		$this->db->createCommand("ALTER TABLE  `loan` ADD  `need_reindex` TINYINT( 1 ) NULL DEFAULT  '0' AFTER  `waiting_time` ,
		ADD INDEX (  `need_reindex` ) ;")->execute();
    }

    public function down()
    {
        echo "m160806_084827_solr_reindex cannot be reverted.\n";

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
