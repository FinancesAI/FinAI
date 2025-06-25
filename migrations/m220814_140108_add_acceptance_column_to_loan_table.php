<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%loan}}`.
 */
class m220814_140108_add_acceptance_column_to_loan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%loan}}', 'acceptance', $this->tinyInteger(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%loan}}', 'acceptance');
    }
}
