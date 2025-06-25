<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%loan_appointment}}`.
 */
class m220913_190554_add_manager_id_columns_to_loan_appointment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%loan_appointment}}', 'manager_id', $this->integer()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%loan_appointment}}', 'manager_id');
    }
}
