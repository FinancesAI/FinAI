<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%source}}`.
 */
class m220605_221218_create_source_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%source}}', [
            'id' => $this->primaryKey(),
            'wordpress_id' => $this->integer(),
            'name' => $this->string(),
            'lead_event_sid' => $this->string(),
            'sales_event_sid' => $this->string(),
            'order' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%source}}');
    }
}
