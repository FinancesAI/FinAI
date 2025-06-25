<?php

use yii\db\Migration;

/**
 * Handles adding lang to table `users_table`.
 */
class m190505_125505_add_lang_to_users_table extends Migration {
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn( 'user', 'lang', $this->string() );
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn( 'user', 'lang' );
	}
}
