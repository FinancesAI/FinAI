<?php

namespace app\models;

use Yii;


class PartialData extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'partial_data';
    }

    public function rules() {
        return [
            [['phone', 'email', 'user_name', 'user_surname', 'personal_code', 'unique_id'], 'string', 'max' => 255],
            [['form_id'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['is_send'], 'boolean'],
            [['unique_id'], 'unique'], 
        ];
    }

  
}
