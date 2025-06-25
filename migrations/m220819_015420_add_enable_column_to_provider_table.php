<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%provider}}`.
 */
class m220819_015420_add_enable_column_to_provider_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%provider}}', 'enable', $this->tinyInteger(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%provider}}', 'enable');
    }
}
