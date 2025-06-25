<?php

use yii\db\Migration;

/**
 * Handles the creation for table `loans_color`.
 */
class m180324_220639_create_loans_color_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('loan_color', [
            'id' => $this->primaryKey(),
	        'name' => $this->string()->unique()->notNull(),
	        'css_color' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('loan_color');
    }
}
