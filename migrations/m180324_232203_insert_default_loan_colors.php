<?php

use yii\db\Migration;

class m180324_232203_insert_default_loan_colors extends Migration
{
    public function up()
    {
	    $this->insert('loan_color', [
	    	'name' => 'White',
			'css_color' => '#fff'
	    ]);
	    $this->insert('loan_color', [
	    	'name' => 'Pink',
			'css_color' => '#f2dede'
	    ]);
	    $this->insert('loan_color', [
	    	'name' => 'Orange',
			'css_color' => 'orange'
	    ]);

    }

    public function down()
    {
	    $this->delete('loan_color', ['name' => 'White']);
	    $this->delete('loan_color', ['name' => 'Pink']);
	    $this->delete('loan_color', ['name' => 'Orange']);
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
