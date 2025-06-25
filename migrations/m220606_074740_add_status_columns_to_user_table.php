<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m220606_074740_add_status_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('user', 'status', $this->integer()->after('role')->defaultValue(0));
        $this->addColumn('user', 'activation_time', $this->integer()->after('update_time'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('user', 'status');
        $this->dropColumn('user', 'activation_time');
    }
}
