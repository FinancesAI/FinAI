<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_enterprise".
 *
 * @property string $id
 * @property string $loan_id
 * @property string $name
 * @property string $nr
 * @property string $address_actual
 * @property string $address_domicile
 * @property string $bank_account_statement
 *
 * @property Loan $loan
 */
class LoanEnterprise extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_enterprise';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id'], 'required'],
            [['loan_id'], 'integer'],
            [['name', 'nr', 'address_actual', 'address_domicile', 'bank_account_statement'], 'string', 'max' => 512],
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
            'name' => Yii::t('app/loan', 'Name'),
            'nr' => Yii::t('app/loan', 'Nr'),
            'address_actual' => Yii::t('app/loan', 'Address Actual'),
            'address_domicile' => Yii::t('app/loan', 'Address Domicile'),
            'bank_account_statement' => Yii::t('app/loan', 'Bank Account Statement'),
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
