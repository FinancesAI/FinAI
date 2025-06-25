<?php

use yii\db\Migration;

/**
 * Class m250116_063810_add_fields_to_loan_table
 */
class m250116_063810_add_fields_to_loan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
        {
          $this->addColumn('{{%loan}}', 'car_ad_link', $this->string(255)->null()->comment('Link to car advertisement if loan is for purchasing a car'));
            $this->addColumn('{{%loan}}', 'company_duration_months', $this->integer()->null()->comment('Company Duration of Operation (months)'));
            $this->addColumn('{{%loan}}', 'true_beneficiary', $this->boolean()->defaultValue(0)->comment('True beneficiary flag'));
            $this->addColumn('{{%loan}}', 'politically_significant', $this->boolean()->defaultValue(0)->comment('Politically significant flag'));
            $this->addColumn('{{%loan}}', 'agree', $this->boolean()->defaultValue(0)->comment('Agree flag'));
            $this->addColumn('{{%loan}}', 'car_brand', $this->string(255)->null()->comment('Car brand'));
            $this->addColumn('{{%loan}}', 'car_model', $this->string(255)->null()->comment('Car model'));
            $this->addColumn('{{%loan}}', 'step_monthly_payment', $this->decimal(10, 2)->null()->comment('Step monthly payment'));
            $this->addColumn('{{%loan}}', 'property_type', $this->string(255)->null()->comment('Property type'));
            $this->addColumn('{{%loan}}', 'loan_purpose', $this->string(255)->null()->comment('Loan purpose'));
        }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%loan}}', 'car_ad_link');
        $this->dropColumn('{{%loan}}', 'company_duration_months');
        $this->dropColumn('{{%loan}}', 'true_beneficiary');
        $this->dropColumn('{{%loan}}', 'politically_significant');
        $this->dropColumn('{{%loan}}', 'agree');
        $this->dropColumn('{{%loan}}', 'car_brand');
        $this->dropColumn('{{%loan}}', 'car_model');
        $this->dropColumn('{{%loan}}', 'step_monthly_payment');
        $this->dropColumn('{{%loan}}', 'property_type');
        $this->dropColumn('{{%loan}}', 'loan_purpose');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250116_063810_add_fields_to_loan_table cannot be reverted.\n";

        return false;
    }
    */
}
