<?php

use yii\db\Migration;

/**
 * Handles adding provider_id to table `user_table`.
 */
class m190417_201300_add_provider_id_to_user_table extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn( 'user', 'provider_id', $this->integer() );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn( 'user', 'provider_id' );
	}
}
