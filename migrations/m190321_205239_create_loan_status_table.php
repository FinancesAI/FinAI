<?php

use yii\db\Migration;

/**
 * Handles the creation for table `status_table`.
 */
class m190321_205239_create_loan_status_table extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->createTable( 'loan_status', [
			'code' => $this->string(),
			'id'   => $this->primaryKey(),
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropTable( 'loan_status' );
	}
}
