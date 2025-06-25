<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partial_data}}`.
 */
class m241017_113545_create_partial_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partial_data}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(255),
            'email' => $this->string(255),
            'user_name' => $this->string(255),
            'user_surname' => $this->string(255),
            'personal_code' => $this->string(255),
            'unique_id' => $this->string(255)->unique(),
            'form_id' => $this->integer(),
            'created_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'is_send' => $this->boolean()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partial_data}}');
    }
}
