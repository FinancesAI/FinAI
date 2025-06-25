<?php

use yii\db\Migration;

/**
 * Handles adding color_column to table `loan`.
 */
class m180324_223518_add_color_column_to_loan_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('loan', 'color_id', $this->integer());

	    $this->addForeignKey(
		    'fk-loan-color_id',
		    'loan',
		    'color_id',
		    'loan_color',
		    'id',
		    'SET NULL'
	    );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('loan', 'color_id');
    }
}
