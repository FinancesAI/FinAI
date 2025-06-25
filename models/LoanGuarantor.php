<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_guarantor".
 *
 * @property string $id
 * @property string $loan_id
 * @property string $full_name
 * @property string $personal_code
 * @property string $workplace
 * @property string $workposition
 * @property string $phone
 * @property string $email
 * @property double $income
 * @property double $outcome
 * @property string $work_experience
 * @property double $address
 * @property double $postcode
 * @property string $bank_statement
 *
 * @property Loan $loan
 */
class LoanGuarantor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_guarantor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id'], 'required'],
            [['loan_id'], 'integer'],
            [['income', 'outcome', 'work_experience'], 'number'],
            [['full_name', 'personal_code', 'workplace', 'workposition', 'phone', 'email', 'address', 'postcode'], 'string', 'max' => 256],
            [['bank_statement'], 'string', 'max' => 512],
            [['loan_id'], 'unique'],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::className(), 'targetAttribute' => ['loan_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/loan', 'ID'),
            'loan_id' => Yii::t('app/loan', 'Loan ID'),
            'full_name' => Yii::t('app/loan', 'Full Name'),
            'personal_code' => Yii::t('app/loan', 'Personal Code'),
            'workplace' => Yii::t('app/loan', 'Workplace'),
            'workposition' => Yii::t('app/loan', 'Workposition'),
            'phone' => Yii::t('app/loan', 'Phone'),
            'email' => Yii::t('app/loan', 'Email'),
            'income' => Yii::t('app/loan', 'Income'),
            'outcome' => Yii::t('app/loan', 'Outcome'),
            'work_experience' => Yii::t('app/loan', 'Work experience'),
            'address' => Yii::t('app/loan', 'Address'),
            'postcode' => Yii::t('app/loan', 'Postcode'),
            'bank_statement' => Yii::t('app/loan', 'Bank Statement'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }
}
