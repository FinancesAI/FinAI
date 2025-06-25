<?php

namespace app\models;

/**
 * This is the model class for table "loan_status".
 *
 * @property string $code
 * @property integer $id
 */
class LoanStatus extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'loan_status';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[ [ 'code' ], 'string', 'max' => 255 ],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'code' => 'Code',
			'id'   => 'ID',
		];
	}

	public function getLoans() {
		return $this->hasMany( Loan::className(), [ 'status' => 'id' ] );
	}
}
