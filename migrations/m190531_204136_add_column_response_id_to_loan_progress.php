<?php

use yii\db\Migration;

/**
 * Handles adding column_response_id to table `loan_progress`.
 */
class m190531_204136_add_column_response_id_to_loan_progress extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn( 'loan_progerss', 'api_response_id', $this->string() );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn( 'loan_progerss', 'api_response_id' );
	}
}
