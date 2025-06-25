<?php

namespace app\models;

use app\services\SourceService;
use himiklab\sortablegrid\SortableGridBehavior;
use vakorovin\datetimepicker\Datetimepicker;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%field}}".
 *
 * @property string $id
 * @property integer $type
 * @property string $name
 * @property integer $filter_type
 * @property integer #original
 */
class Field extends \yii\db\ActiveRecord
{
    private $_model;

    const TYPE_EQAUL = 2;
    const TYPE_LIKE_START = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%field}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['type', 'sort_order', 'filter_type', 'original'], 'integer'],
            [['name'], 'string', 'max' => 250],
            [['type'], 'unique', 'targetAttribute' => ['type', 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/field', 'ID'),
            'type' => Yii::t('app/field', 'Type'),
            'sort_order' => Yii::t('app/field', 'Order'),
            'type-1' => Yii::t('app/field', 'Person'),
            'type-2' => Yii::t('app/field', 'Loan'),
            'enable-0' => Yii::t('app/field', 'No'),
            'enable-1' => Yii::t('app/field', 'Yes'),
            'name' => Yii::t('app/field', 'Name'),
            'filter_type' => Yii::t('app/field', 'Filter  type'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTypes() {
        return [
            Person::TYPE => Yii::t("app/field", "Person"),
            Loan::TYPE => Yii::t("app/field", "Loan"),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getEnableTypes() {
        return [
            1 => Yii::t("app/field", "Yes"),
            0 => Yii::t("app/field", "No"),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFilterTypes() {
        return [
            0 => Yii::t("app/field", "%LIKE%"),
            1 => Yii::t("app/field", "%LIKE"),
            2 => Yii::t("app/field", "="),
        ];
    }

    /**
     *
     * @param integer $type
     * @param ActiveForm $form
     * @param Loan $model
     */
    public function getFormFields($type, $view, $form, $model, $modelPerson = null) {
        $fieldsEnabled = $this->_getViews($type, $view);

        foreach ($fieldsEnabled as $field) {
            if (strpos($field->name, 'person.') !== false) {
                echo $this->_getFormField($form, $modelPerson, str_replace("person.", "", $field->name));
            } elseif (strpos($field->name, 'loan.') !== false) {
                echo $this->_getFormField($form, $modelPerson, str_replace("loan.", "", $field->name));
            } else {
                echo $this->_getFormField($form, $model, $field->name);
            }
        }
    }

    /**
     * Get form field, check defined
     * @param ActiveForm $form
     * @param Loan $model
     * @param string $fieldName
     */
    private function _getFormField($form, $model, $fieldName) {
        if ($fieldName == "user_id") {
            echo $form->field($model, $fieldName)->dropDownList(ArrayHelper::map(User::find()->where(["role" => 0])->asArray()->all(), 'id', 'fullname'),['class'=>'form-control','prompt' => '-']);
        } elseif ($fieldName == "status") {
            echo $form->field($model, $fieldName)->dropDownList($model->getStatuses());
        } elseif ($fieldName == "deal_stage") {
            $stages = $model->getDealStages();
            unset($stages[1]);
            unset($stages[2]);
            echo $form->field($model, $fieldName)->dropDownList($stages, ["id" => "deal"]);
        } elseif ($fieldName == "deal_product") {
            echo $form->field($model, $fieldName)->dropDownList(ArrayHelper::map($model->getDealStageProduct()[(int)$model->deal_stage], 'id', 'title'), ["id" => "deal-product"]);
        } elseif ($fieldName == "approved") {
            echo $form->field($model, $fieldName)->dropDownList($model->getInProgress());
        } elseif ($fieldName == "source") {
            echo $form->field($model, $fieldName)->dropDownList($model->getSources());
        } elseif ($fieldName == "is_courier_sent") {
            echo $form->field($model, $fieldName)->dropDownList($model->getCourier());
        } elseif ($fieldName == "education") {
            echo $form->field($model, $fieldName)->dropDownList($model->getEducation());
        } elseif ($fieldName == "family_status") {
            echo $form->field($model, $fieldName)->dropDownList($model->getFamilyStatus());
        } elseif ($fieldName == "actions") {
            echo $form->field($model, $fieldName)->dropDownList($model->getActions(), ["multiple" => "multiple"]);
        } elseif ($fieldName == "credit_history") {
            echo $form->field($model, $fieldName)->dropDownList($model->getCreditHistory());
        } elseif ($fieldName == "product") {
            echo $form->field($model, $fieldName)->dropDownList($model->getProducts());
        } elseif ($fieldName == "close_time") {
            echo "<a id=\"datepicker-closetime-label\" data-value=\"".$model->close_time."\"><b>".Yii::t("app/loan", "Close time")."</b></a>";
            echo "<span class=\"glyphicon glyphicon-remove\" id=\"datepicker-closetime-remove\"></span>";
            echo $form->field($model, $fieldName)->widget(Datetimepicker::classname(), [
                'options' => ["format" => "unixtime", "class" => "hide", "id" => "datepicker-closetime"]
            ])->label(false);
        } else {
            echo $form->field($model, $fieldName)->textInput();
        }
    }

    /**
     * Get custum field rules should be merged with existing rules
     * @param integer $type
     * @return array
     */
    public function getCustomFieldRules($type) {
        $fieldsEnabled = $this->_getColumnsEnabled($type);

        $attr = [];
        foreach ($fieldsEnabled as $field) {
            $attr[] = str_replace(".", "_", $field->name);
        }

        return [$attr, 'safe'];
    }

    /**
     * replace person.name with person_name
     * @param unknown $attr
     * @param unknown $type
     * @return mixed
     */
    public function getSearchAttributes($attr, $type) {
        $fieldsEnabled = $this->_getColumnsEnabled($type);

        foreach ($fieldsEnabled as $field) {
            $attr[] = str_replace(".", "_", $field->name);
        }

        return $attr;
    }

    /**
     * Setup search sort
     * @param unknown $dataProvider
     * @param unknown $type
     */
    public function setSearchSort ($dataProvider, $type, $view) {
        $fieldsEnabled = $this->_getViews($type, $view);

        foreach ($fieldsEnabled as $field) {
            $dataProvider->sort->attributes[str_replace(".", "_", $field->name)] = [
                'asc' => [$field->name => SORT_ASC],
                'desc' => [$field->name => SORT_DESC],
            ];
        }

        return $dataProvider;
    }

    /**
     * Setup search filter
     * @param unknown $query
     * @param unknown $type
     * @param unknown $class
     * @return unknown
     */
    public function setSearchFilter ($query, $type, $class) {
        $fieldsEnabled = $this->_getColumnsEnabled($type);

        foreach ($fieldsEnabled as $field) {
            $modelValue = $class->{str_replace(".", "_", $field->name)};

            if ($field->name == "prog" || $field->name == "ceo" || $field->name == "files" || $field->name == "loan.files" || $field->name == "loan_files") {
                continue;
            }

            if ($field->name == "create_time" || $field->name == "update_time" || $field->name == "close_time") {
                $prefix = "";
                if ($field->getTableNameByType() == Loan::tableName())
                    $prefix = "loan.";
                if ($field->getTableNameByType() == Person::tableName())
                    $prefix = "person.";

                if ($field->name == "create_time" && $modelValue)
                    $query->andFilterWhere(['between', $prefix.'create_time', strtotime($modelValue), strtotime('+1 day', strtotime($modelValue))]);

                if ($field->name == "update_time" && $modelValue)
                    $query->andFilterWhere(['between', $prefix.'update_time', strtotime($modelValue), strtotime('+1 day', strtotime($modelValue))]);

                if ($field->name == "close_time" && $modelValue)
                    $query->andFilterWhere(['between', $prefix.'close_time', strtotime($modelValue), strtotime('+1 day', strtotime($modelValue))]);
                continue;
            }

            if ($field->name == "actions") {
                if (!empty($modelValue)) {
                    foreach ($modelValue as $action) {
                        $query->andFilterWhere(['like', $field->name, $action]);
                    }
                    continue;
                }
            }

            if ($field->name == "id") {
                $query->andFilterWhere(["loan.".$field->name => $modelValue]);
                continue;
            }


            if ($field->filter_type == Field::TYPE_EQAUL) {
                $query->andFilterWhere([$field->name => $modelValue]);
            } elseif ($field->filter_type == Field::TYPE_LIKE_START) {
                $query->andFilterWhere(['like ', $field->name, $modelValue."%"]);
            } else {
                $query->andFilterWhere(['like', $field->name, $modelValue]);
            }
        }

        if ($type == Loan::TYPE)
            $query->andFilterWhere(["person_id" => $class->person_id]);

        return $query;
    }

    /**
     * Set model
     * @param Loan $model
     */
    public function setSearchModel($model) {
        $this->_model = $model;
    }

    /**
     * Get fields for gridview and detalview widgets
     * @param integer $type
     * @param boolean $isDetalWidget
     * @param boolean $onlyType
     * @return array
     */
    public function getFields($type, $view, $isDetalWidget = false, $onlyType = false) {
        $fieldsEnabled = $this->_getViews($type, $view);

        $fields = [];
        foreach ($fieldsEnabled as $field) {
            if ($isDetalWidget) {

                if ($onlyType == $type) {
                    if (strpos($field->name, 'person.') === false) {
                        $fieldData = $this->_getFieldValue($type, $field->name, $isDetalWidget);
                    } elseif (strpos($field->name, 'loan.') === false) {
                        $fieldData = $this->_getFieldValue($type, $field->name, $isDetalWidget);
                    } else {
                        continue;
                    }
                } else {
                    if (strpos($field->name, 'person.') !== false) {
                        $fieldData = $this->_getFieldValue($type, $field->name, $isDetalWidget);
                    } elseif (strpos($field->name, 'loan.') !== false) {
                        $fieldData = $this->_getFieldValue($type, $field->name, $isDetalWidget);
                    } else {
                        $fieldData = $this->_getFieldValue($type, str_replace(".", "_", $field->name), $isDetalWidget);
                    }
                }
            } else {
                $filter = $this->_getFieldFilter($type, $field->name);

                if ($filter) {
                    $fieldData = [
                        'attribute'=> str_replace(".", "_", $field->name),
                        'value'=> $this->_getFieldValue($type, $field->name),
                        'filter'=> $filter,
                        'format' => 'raw'
                    ];
                } else {
                    $fieldData = [
                        'attribute'=> str_replace(".", "_", $field->name),
                        'value'=> $this->_getFieldValue($type, $field->name),
                        'format' => 'raw'
                    ];
                }
            }
             if ($fieldData['value'] != NULL && $fieldData['value'] != '')
              {
                    $fields[] = $fieldData;
               }     
        }

        if (!$fields) {
            return [
                "id"
            ];
        }
//print_r($fields);
        return $fields;
    }

    /**
     * Get field values for gridview and detalview widgets
     * @var Loan $model
     * @param integer $type
     * @param string $name
     * @param boolean $isDetalWidget
     * @return mixed
     */
    private function _getFieldValue($type, $name, $isDetalWidget = false) {
        if ($isDetalWidget) {

            if ($name ==  "create_time" ||  $name == "update_time" || $name == "close_time" || $name == "person.create_time" || $name == "person.update_time") {
                return [
                    'label' => (isset($this->_model->attributeLabels()[$name])) ? $this->_model->attributeLabels()[$name] : "No label",
                    'value' => ($this->_model->getAttribute($name)) ? Yii::$app->formatter->asDate($this->_model->getAttribute($name)) : null,
                ];
            }

            if ( $name == 'document' ) {
                return [
                    'label'   => Yii::t( 'app/loan', 'Document' ),
                    'value'   => $this->_model->getAttribute( $name )
                        ? '<a href="https://www.finlat.lv/document/' . $this->_model->getAttribute( $name )
                        . '" target="_blank">' . Yii::t( 'app/loan', 'Document' ) . '</a>'
                        : '-',
                    'format'  => 'raw',
                    'options' => [ 'target' => '_blank' ]
                ];
            }
            if ($name == 'source') {
//                $params = \Yii::$app->params['wordpress']['forms'];
                $params = SourceService::getWordpressForms();
                $value = $this->_model->getAttribute($name);

                return [
                    'label' => Yii::t('app/loan', 'source'),
                    'value' => isset($params[$value]) ? $params[$value]['name'] : $this->_model->getAttribute($name)
                ];

            }
        if ($name == 'car_ad_link') {
            $params = SourceService::getWordpressForms();
            $value = $this->_model->getAttribute($name);
            $url = isset($params[$value]) ? $params[$value]['url'] : (isset($this->_model->$name) ? $this->_model->$name : '');
            if ($url) {
                $displayUrl = strlen($url) > 10 ? substr($url, 0, 30) . '...' : $url;
              return [
                    'label' => Yii::t('app/loan', 'Link to car advertisement'),
                     'value' => \yii\helpers\Html::a($displayUrl, $url, ['target' => '_blank']),
                     'format' => 'raw',
        ];
            }
        }
        if ($name == 'realEstate') {
            $params = SourceService::getWordpressForms();
            $value = $this->_model->getAttribute($name);
            if ($value=='J') {
               return [
                    'label' => Yii::t('app/loan', 'Real Estate'),
                    'value' => Yii::t('app/loan', 'YES'),
                    'format' => 'raw',
                ];        
            }else {
               return [
                    'label' => Yii::t('app/loan', 'Real Estate'),
                    'value' => Yii::t('app/loan', 'NO'),
                    'format' => 'raw',
                ];
            }
        } 


            if (strpos($name, 'person.') !== false) {
                $value = $this->_model->getValue($name, $this->_model->person);
            } elseif (strpos($name, 'loan.') !== false) {
                $value = $this->_model->getValue($name, $this->_model->loan);
            } else {
                $value = $this->_model->getValue($name, $this->_model);
            }

            return [
                'label' => $this->_model->getAttributeLabel($name),
                'value' => $value,
            ];

        } else {

            if ($name ==  "create_time" ||  $name == "update_time" || $name == "close_time") {
                return function ($model) use ($name) {
                    return ($model->$name) ? Yii::$app->formatter->asDate($model->$name) : null;
                };
            }


            if ($name == 'referral') {

                return function ($model) use ($name) {

                    $value = json_decode($model->$name, true);

//                    $referral = '';
//
//                    if ($value['utm_source']) {
//                        $referral .= 'Utm Source: ' . $value['utm_source'] . "\r\n";
//                    }
//
//                    if ($value['utm_medium']) {
//                        $referral .= 'Utm Medium: ' . $value['utm_medium'] . "\r\n";
//                    }
//
//                    if ($value['utm_campaign']) {
//                        $referral .= 'Utm Campaign: ' . $value['utm_campaign'] . "\r\n";
//                    }
                    return $value ? implode(', ', $value) : null;
                };
            }

            return function ($model) use ($name) {
                if (strpos($name, 'person.') !== false) {
                    return $model->getValue($name, $model->person);
                } elseif (strpos($name, 'loan.') !== false) {
                    return $model->getValue($name, $model->loan);
                } else {
                    return $model->getValue($name, $model);
                }
            };
        }
        return $name;
    }

    /**
     * Get field filter for gridview and detalview widgets
     * @param integer $type
     * @param string $name
     * @return mixed
     */
    private function _getFieldFilter($type, $name) {
        if ($name ==  "create_time" || $name == "update_time" || $name == "close_time") {
            return Datetimepicker::widget([
                'model' => $this->_model,
                'attribute' => $name,
                'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'format' => 'd.m.Y', 'timepicker' => false]
            ]);
            return Html::activeDropDownList($this->_model, 'status', [],['class'=>'form-control','prompt' => 'All']);
        }

        if ($type == Loan::TYPE) {
            if ($name ==  "user_id") {
                return Html::activeDropDownList($this->_model, 'user_id', ArrayHelper::map(User::find()->where(["role" => 0])->asArray()->all(), 'id', 'fullname'),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "status") {
                return Html::activeDropDownList($this->_model, 'status', $this->_model->getStatuses(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "actions") {
                return Html::activeDropDownList($this->_model, 'actions', $this->_model->getActions(),['class'=>'form-control', 'multiple' => 'multiple','prompt' => 'All']);
            }
            if ($name ==  "approved") {
                return Html::activeDropDownList($this->_model, 'approved', $this->_model->getInProgress(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "bill_status") {
                return Html::activeDropDownList($this->_model, 'bill_status', (new Bill())->getStatuses(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "product") {
                return Html::activeDropDownList($this->_model, 'product', $this->_model->getProducts(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "source") {
                $sources = $this->_model->getSources();
                $sources = array_filter($sources, function ($var) {
                    return !empty($var) && !is_numeric($var);
                });
                return Html::activeDropDownList($this->_model, 'source', $sources,['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "is_courier_sent") {
                return Html::activeDropDownList($this->_model, 'is_courier_sent', $this->_model->getCourier(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "deal_stage") {
                return Html::activeDropDownList($this->_model, 'deal_stage', $this->_model->getDealStages(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "person.credit_history") {
                return Html::activeDropDownList($this->_model, 'person_credit_history', (new Person())->getCreditHistory(),['class'=>'form-control','prompt' => 'All']);
            }
        }

        if ($type == Person::TYPE) {
            if ($name ==  "credit_history") {
                return Html::activeDropDownList($this->_model, 'credit_history', $this->_model->getCreditHistory(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "family_status") {
                return Html::activeDropDownList($this->_model, 'family_status', $this->_model->getFamilyStatus(),['class'=>'form-control','prompt' => 'All']);
            }
            if ($name ==  "education") {
                return Html::activeDropDownList($this->_model, 'education', $this->_model->getEducation(),['class'=>'form-control','prompt' => 'All']);
            }
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::save($runValidation, $attributeNames)
     * Creates new table row
     */
    public function save($runValidation = true, $attributeNames = null) {
        $isNew = $this->isNewRecord;
        if (parent::save($runValidation, $attributeNames)) {
            if ($isNew) {
                if ($this->_newPersonRelationCheck()) {

                    return true;
                }
                if ($this->_newColumnExistsCheck($this->getTableNameByType())) {
                    return true;
                }
                return (boolean)Yii::$app->db->createCommand("ALTER TABLE  ".$this->getTableNameByType()." ADD  `".$this->name."` VARCHAR( 250 ) NULL ;")->execute();
            }
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\ActiveRecord::delete()
     * Delete table row
     */
    public function delete($safe = null) {
        Yii::$app->cache->flush();

        if ($this->original)
            return false;

        if ($this->_newPersonRelationCheck())
            $safe = true;

        if ($safe)
            return parent::delete();

        if (parent::delete())
            return (boolean)Yii::$app->db->createCommand("ALTER TABLE ".$this->getTableNameByType()." DROP ".$this->name.";")->execute();

        return false;
    }

    /**
     * Check if field name person.id
     */
    private function _newPersonRelationCheck() {
        if (strpos($this->name, 'person.') !== false) {
            return true;
        }

        if (strpos($this->name, 'loan.') !== false) {
            return true;
        }
        return false;
    }

    /**
     * Check if new column exists or not
     * @return boolean
     */
    private function _newColumnExistsCheck($tableName) {
        $columns = Yii::$app->db->createCommand("SHOW COLUMNS from ".$tableName.";")->queryAll();
        foreach ($columns as $column) {
            if ($column["Field"] == $this->name)
                return true;
        }
        return false;
    }

    /**
     * Get table name by model type
     * @return string
     */
    public function getTableNameByType() {
        if ($this->type == Person::TYPE)
            return Person::tableName();

        if ($this->type == Loan::TYPE)
            return Loan::tableName();

        return null;
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::afterSave($insert, $changedAttributes)
     */
    public function afterSave($insert, $changedAttributes) {
        Yii::$app->getCache()->flush();

        parent::afterSave($insert, $changedAttributes);
    }

    private function _getViews($type, $view) {
        $role = 0;
        if (method_exists(Yii::$app, "getUser")) {
            $role = Yii::$app->user->getIdentity()->role;
        }

        $params = ["field_view.view" => $view, "field_view.show" => 1];
        if ($role == 0) {
            $params["field_view.role"] = $role;
        }

        $cacheSlug = "column-view-".$view."-".$role;

        if ($cachedData = Yii::$app->getCache()->get($cacheSlug)) {
            return $cachedData;
        }

        $fields = Field::find()->where($params)->joinWith("fieldView")->orderBy("field_view.sort_order")->all();

        Yii::$app->getCache()->add($cacheSlug, $fields);

        return $fields;
    }

    /**
     * Get columns enabled
     * @param unknown $type
     * @param string $customOnly
     * @return Field
     */
    private function _getColumnsEnabled($type, $customOnly = false, $formEnabled = false) {
        $role = 0;
        if (method_exists(Yii::$app, "user") && method_exists(Yii::$app->getUser(), "identity")) {
            $role = Yii::$app->user->identity->role;
        }

        $cacheSlug = "column-field-".$type."-".$role."-".(int)$customOnly."-".(int)$formEnabled;

        if ($cachedData = Yii::$app->getCache()->get($cacheSlug)) {
            return $cachedData;
        }

        if ($role !== 0) {
            $params = [
                "type" => $type,
            ];
        } else {
            $params = [
                "type" => $type,
            ];
        }

        if ($customOnly) {
            $params["original"] = 0;
        }

        $models = self::find()->where($params)->orderBy("sort_order asc")->all();

        Yii::$app->getCache()->add($cacheSlug, $models);

        return $models;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldView()
    {
        return $this->hasOne(FieldView::className(), ['field_id' => 'id']);
    }
}