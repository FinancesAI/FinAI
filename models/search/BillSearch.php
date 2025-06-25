<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bill;

/**
 * BillSearch represents the model behind the search form about `app\models\Bill`.
 */
class BillSearch extends Bill
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id'], 'integer'],
        	[['amount'], 'number'],
            [['type', 'status', 'create_time', 'update_time', 'close_time', 'due_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
	
    public function beforeValidate() {
    	return true;
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
        $query = Bill::find();
		//$query->joinWith(["loan"]);
		
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        	'pagination' => [
        			'pagesize' => Yii::$app->setting->get("page_limit"),
        	],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
		
		if (\Yii::$app->getRequest()->get("unpaid")) {
			$query->andFilterWhere(["<", "due_time", time()]);
			$query->andFilterWhere(["status" => 0]);
		}

        $query->andFilterWhere([
            'id' => $this->id,
            'loan_id' => $this->loan_id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount,
        ]);

        if ($this->create_time)
        	$query->andFilterWhere(['between', 'create_time', strtotime("midnight", strtotime($this->create_time)), strtotime("tomorrow", strtotime($this->create_time)) - 1]);
        
        if ($this->due_time)
        	$query->andFilterWhere(['between', 'due_time', strtotime("midnight", strtotime($this->due_time)), strtotime("tomorrow", strtotime($this->due_time)) - 1]);

        if ($this->close_time)
        	$query->andFilterWhere(['between', 'close_time', strtotime("midnight", strtotime($this->close_time)), strtotime("tomorrow", strtotime($this->close_time)) - 1]);
         
        return $dataProvider;
    }
}
