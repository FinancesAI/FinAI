<?php

use yii\db\Migration;

/**
 * Handles adding access_token_column to table `users_table`.
 */
class m190529_212837_add_access_token_column_to_users_table extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn( 'user', 'access_token', $this->string() );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn( 'user', 'access_token' );
	}
}
