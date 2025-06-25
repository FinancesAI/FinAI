<?php

use yii\db\Migration;

/**
 * Handles adding source_id_column to table `loan_table`.
 */
class m190604_195028_add_source_id_column_to_loan_table extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn( 'loan', 'source_id', $this->integer() );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn( 'loan', 'source_id' );
	}
}
