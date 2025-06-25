<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_color".
 *
 * @property integer $id
 * @property string $name
 * @property string $css_color
 *
 * @property Loan[] $loans
 */
class LoanColor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'css_color'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'css_color' => 'Css Color',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getLoans()
//    {
//        return $this->hasMany(Loan::className(), ['color_id' => 'id']);
//    }
}
