<?php

namespace app\models;

use Yii;

use app\components\SortableGridBehaviorExtanded;

/**
 * This is the model class for table "field_view".
 *
 * @property string $id
 * @property string $field_id
 * @property integer $view
 * @property integer $role
 * @property integer $show
 * @property string $sort_order
 *
 * @property Field $field
 */
class FieldView extends \yii\db\ActiveRecord
{
	const VIEW_PERSON_TABLE = 1;
	const VIEW_PERSON_VIEW = 2;
	const VIEW_PERSON_FORM_EDIT = 3;
	const VIEW_PERSON_FORM_CREATE = 4;
	const VIEW_LOAN_TABLE = 5;
	const VIEW_LOAN_VIEW = 6;
	const VIEW_LOAN_FORM_EDIT = 7;
	const VIEW_LOAN_FORM_CREATE = 8;
	
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'field_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_id', 'view'], 'required'],
            [['field_id', 'view', 'role', 'show', 'sort_order'], 'integer'],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    			'sort' => [
    					'class' => SortableGridBehaviorExtanded::className(),
    					'sortableAttribute' => 'sort_order'
    			],
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/field', 'ID'),
            'field_id' => Yii::t('app/field', 'Field ID'),
            'view' => Yii::t('app/field', 'View'),
            'show' => Yii::t('app/field', 'Show'),
            'role' => Yii::t('app/field', 'Role'),
        	'sort_order' => Yii::t('app/field', 'Sort Order'),
        ];
    }
    
    public function getViews() {
    	return [
    			"1" => Yii::t('app/field', 'Person table'),
    			"2" => Yii::t('app/field', 'Person view table'),
    			"3" => Yii::t('app/field', 'Person edit form'),
    			"4" => Yii::t('app/field', 'Person create form'),
    			"5" => Yii::t('app/field', 'Loan table'),
    			"6" => Yii::t('app/field', 'Loan view table'),
    			"7" => Yii::t('app/field', 'Loan edit form'),
    			"8" => Yii::t('app/field', 'Loan create form'),
    	];
    }
	
    public function saveFields() {
    	foreach (Yii::$app->request->post("fields") as $fieldId => $fieldData) {
    		$field = FieldView::find()->where(["view" => Yii::$app->request->get("id"), "field_id" => $fieldId])->one();
    		if (!$field) {
    			$field = new FieldView();
    		}
    		$field->field_id = $fieldId;
    		$field->role = (int)(isset($fieldData["role"]) ? $fieldData["role"] : 0);
    		$field->show = (int)(isset($fieldData["show"]) ? $fieldData["show"] : 0);
    		$field->view = Yii::$app->request->get("id");
    		$field->save();
    	}
    	Yii::$app->cache->flush();
    	return true;
    }
    
    public function getTypeById($id) {
    	if (in_array($id, ["1", "2", "3", "4"])) {
    		return Person::TYPE;
    	}
    	
    	if (in_array($id, ["5", "6", "7", "8"])) {
    		return Loan::TYPE;
    	}
    }
    
	public static function findFields($id) {
    	if (in_array($id, ["1", "2", "3", "4"])) {
			return Field::find()->where(["type" => Person::TYPE])->joinWith("fieldView")->andWhere(["field_view.view" => $id])->orderBy("field_view.sort_order");
		}
		
    	if (in_array($id, ["5", "6", "7", "8"])) {
			return Field::find()->where(["type" => Loan::TYPE])->joinWith("fieldView")->andWhere(["field_view.view" => $id])->orderBy("field_view.sort_order");
		}
		
		return [];
	}
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }
}
