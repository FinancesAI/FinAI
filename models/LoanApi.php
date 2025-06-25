<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_api".
 *
 * @property string $id
 * @property string $url_hash
 * @property string $password
 * @property integer $type
 * @property integer $loan_id
 * @property integer $used
 * 
 * @property Loan $loan
 */
class LoanApi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_api';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_hash', 'password', 'type', 'loan_id'], 'required'],
            [['type', 'loan_id'], 'integer'],
            [['url_hash', 'password'], 'string', 'max' => 250],
            [['url_hash'], 'unique'],
            [['used'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/loan', 'ID'),
            'url_hash' => Yii::t('app/loan', 'Url Hash'),
            'password' => Yii::t('app/loan', 'Password'),
            'type' => Yii::t('app/loan', 'Type'),
            'loan_id' => Yii::t('app/loan', 'Loan ID'),
        ];
    }
    
    public static function getTypes() {
    	return [
    			"0" => "None",
    			"1" => "Auto loan form",
    			"2" => "Loan merge form",
    			"3" => "Bank statment form",
    			"4" => "VSAA form",
    			"5" => "VSAA EP52 form",
    			"6" => "Document form",
    			"7" => "Guarantor form",
    			"8" => "Enterprise form",
    			"9" => "Loan additional info form",
    			"10" => "Loan merge files form",
    			"11" => "Loan abroad workers",
    			"12" => "Loan abroad workers 2",
    			"13" => "Auto loan form 2",
    			"14" => "Loan merge form 2",
    			"15" => "Auto loan form + bank account statement",
    	];
    }
    
    public function generateHash() {
    	$salt = time();
    	return crypt($this->loan_id, $salt).crypt(time(), $salt).crypt(md5(time()), $salt).crypt($this->loan_id, $salt);
    }
    
    public function generatePassword($password) {
    	return $password;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
    	return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    
    }
}
