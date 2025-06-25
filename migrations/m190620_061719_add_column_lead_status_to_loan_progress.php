<?php

use yii\db\Migration;

/**
 * Handles adding column_lead_status to table `loan_progress`.
 */
class m190620_061719_add_column_lead_status_to_loan_progress extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn( 'loan_progerss', 'lead_status', $this->integer() );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn( 'loan_progerss', 'lead_status' );
	}
}
