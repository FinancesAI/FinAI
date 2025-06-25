<?php

use yii\db\Migration;

class m160728_074034_loan_lang extends Migration
{
    public function up()
    {
$this->db->createCommand("ALTER TABLE  `loan` ADD  `lang` CHAR( 2 ) NULL AFTER  `ip_ountry` ;")->execute();
    }

    public function down()
    {
        echo "m160728_074034_loan_lang cannot be reverted.\n";

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
