<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Person;
use app\models\FieldView;
use app\components\SolrDataProvider;

/**
 * PersonSearch represents the model behind the search form about `app\models\Person`.
 */
class PersonSearch extends Person
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'income', 'outcome', 'dependants', 'credit_history', 'loan_count'], 'integer'],
            [['name', 'surname', 'personal_code', 'phone', 'email', 'description', 'create_time', 'update_time'], 'safe'],
        	Yii::$app->field->getCustomFieldRules(self::TYPE),	
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\ActiveRecord::attributes()
     * Rewrite person.name to person_name
     */
    public function attributes() {
    	return Yii::$app->field->getSearchAttributes(parent::attributes(), Person::TYPE);
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Person::find();
        $query->joinWith("loan");
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        	'pagination' => [
        			'pagesize' => Yii::$app->setting->get("page_limit"),
       		],
        ]);

        if ($q = \Yii::$app->getRequest()->get("q")) {
        	 
        	if (Yii::$app->setting->get("solr") == 1) {
        		$dataProvider = new SolrDataProvider([
        				'pagination' => [
        						'pagesize' => Yii::$app->setting->get("page_limit"),
        						'defaultPageSize' => Yii::$app->setting->get("page_limit"),
        				],
        				'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        		]);
        		
        		$dataProvider->setModelQuery("asd");
        		$dataProvider->setClassName("app\models\Person");
        		$dataProvider->solr->setCollectionUrlByType(1);
        		$q = $dataProvider->solr->escape($q);
        		$dataProvider->solr->setQuery("(name:({$q})*) OR (surname:({$q})*) OR (email:({$q})*) OR (phone:({$q})*) OR (personal_code:({$q})*)");
        	} else {
        		$query->orFilterWhere(['like', 'person.name', $q])
        		->orFilterWhere(['like', 'person.surname', $q])
        		->orFilterWhere(['like', 'person.email', $q])
        		->orFilterWhere(['like', 'person.phone', $q])
        		->orFilterWhere(['like', 'person.personal_code', $q]);
        	}
        
        } else {
        	if (Yii::$app->setting->get("solr") == 1) {
        		$this->load($params);
        
        		$dataProvider = new SolrDataProvider([
        				'pagination' => [
        						'pagesize' => Yii::$app->setting->get("page_limit"),
        						'defaultPageSize' => Yii::$app->setting->get("page_limit"),
        				],
        				'sort' => [
        						'attributes' => $this->attributes(),
        				],
        		]);
        
        		$dataProvider->setClassName("app\models\Person");
        		$dataProvider->solr->setCollectionUrlByType(1);
        		$dataProvider->solr->buildQuery($this->getAttributes(), $dataProvider->className);
        
        	} else {
		        $this->load($params);
		
		        $dataProvider = Yii::$app->field->setSearchSort($dataProvider, Person::TYPE, FieldView::VIEW_PERSON_TABLE);
		        
		        $query = Yii::$app->field->setSearchFilter($query, Person::TYPE, $this);
        	}
        }
        
        return $dataProvider;
    }
}
