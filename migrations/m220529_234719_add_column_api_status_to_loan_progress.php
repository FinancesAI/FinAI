<?php

use yii\db\Migration;

/**
 * Class m220529_234719_add_column_api_status_to_loan_progress
 */
class m220529_234719_add_column_api_status_to_loan_progress extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn( 'loan_progerss', 'api_status', $this->string()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn( 'loan_progerss', 'api_status' );
    }

}
