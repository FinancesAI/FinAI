<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "LoanAppointment".
 *
 * @property string $id
 * @property string $loan_id
 * @property integer $date
 *
 * @property Loan $loan
 */
class LoanAppointment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'date'], 'required'],
            [['loan_id', 'date', 'manager_id'], 'integer'],
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
            'date' => Yii::t('app/loan', 'Date'),
            'manager_id' => Yii::t('app/loan', 'Manager ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id']);
    }
}
