<?php

use yii\db\Migration;

/**
 * Class m250116_065236_add_fields_to_person_table
 */
class m250116_065236_add_fields_to_person_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%person}}', 'marital_status', $this->string(250)->null());
        $this->addColumn('{{%person}}', 'house_number', $this->string(50)->null());
        $this->addColumn('{{%person}}', 'flat_number', $this->string(50)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%person}}', 'marital_status');
        $this->dropColumn('{{%person}}', 'house_number');
        $this->dropColumn('{{%person}}', 'flat_number');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250116_065236_add_fields_to_person_table cannot be reverted.\n";

        return false;
    }
    */
}
