<?php

use yii\db\Migration;

/**
 * Class m241021_111707_add_time_field_to_partial_data_table
 */
class m241021_111707_add_time_field_to_partial_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->addColumn('{{%partial_data}}', 'time', $this->dateTime()->after('form_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
          $this->dropColumn('{{%partial_data}}', 'time');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241021_111707_add_time_field_to_partial_data_table cannot be reverted.\n";

        return false;
    }
    */
}
