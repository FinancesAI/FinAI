<?php

namespace app\models\search;

use app\models\FieldView;
use app\models\Loan;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * LoanSearch represents the model behind the search form about `app\models\Loan`.
 */
class LoanSearch extends Loan {
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'id',
                    'person_id',
                    'user_id',
                    'amount',
                    'term',
                    'status',
                    'product',
                    'approved',
                    'deal_stage',
                    'deal_product',
                    'waiting_time',
                    'person.create_time',
                ],
                'integer'
            ],
            [
                [
                    'source',
                    'refferal',
                    'query_string',
                    'ip_ountry',
                    'actions',
                    'description',
                    'person',
                    'create_time',
                    'update_time',
                    'close_time',
                    'person.name',
                    'person.phone',
                    'person.surname',
                    'person.personal_code',
                    'person.email',
                ],
                'safe'
            ],
            [ [ 'person_name', ], 'safe' ],
            Yii::$app->field->getCustomFieldRules( self::TYPE ),
        ];
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\ActiveRecord::attributes()
     * Rewrite person.name to person_name
     */
    public function attributes() {
        $attributes = Yii::$app->field->getSearchAttributes( parent::attributes(), Loan::TYPE );

//
        return array_merge( $attributes, [ 'person.name', 'person.personal_code', 'person.email', 'person.phone', 'person.create_time', 'person.surname' ] );
//    	return Yii::$app->field->getSearchAttributes(parent::attributes(), Loan::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search( $params ) {
        $query = Loan::find();
        $query->joinWith( "person" );

        $query->andFilterWhere( [ 'LIKE', 'person.name', $this->getAttribute( 'person.name' ) ] );


        if (isset($params)
            && isset($params['LoanSearch'])
            && isset($params['LoanSearch']['source'])
            && in_array($params['LoanSearch']['source'], $this->sourceRealEstate())) {
            $params['LoanSearch']['source'] = 7;


            //$this->sourceId( $this->sourceRealEstate() )
            // TODO: for some reason this is not working
//            $query->andFilterWhere(['or', $this->sourceId($this->sourceRealEstate())]);
//            $query->andFilterWhere(['source' => 7]);
        }

//		dd($params);
        //Kredīts pret nekustamo īpašumu === $this->sourceRealEstate()

        $dataProvider = new ActiveDataProvider( [
            'query'      => $query,
            'sort'       => [ 'defaultOrder' => [ 'create_time' => SORT_DESC ] ],
            'pagination' => [
                'pagesize' => Yii::$app->setting->get( "page_limit" ),
            ],
        ] );

        $dataProvider->sort->attributes['person.name'] = [
            'asc'  => [ 'person.name' => SORT_ASC ],
            'desc' => [ 'person.name' => SORT_DESC ],
        ];

        if (isset($params['acceptance'])) {
            $dataProvider->query->andWhere(['acceptance' => 1]);
        }

        if ( $q = \Yii::$app->getRequest()->get( "q" ) ) {

            $query->orFilterWhere( [ 'like', 'person.name', $q ] )
                ->orFilterWhere( [ 'like', 'person.surname', $q ] )
                ->orFilterWhere( [ 'like', 'person.email', $q ] )
                ->orFilterWhere( [ 'like', 'person.phone', $q ] )
                ->orFilterWhere( [ 'like', 'person.create_time', $q ] )
                ->orFilterWhere( [ 'like', 'person.personal_code', $q ] );
        } else {

            $this->load( $params );

            $dataProvider = Yii::$app->field->setSearchSort( $dataProvider, Loan::TYPE, FieldView::VIEW_LOAN_TABLE );

            Yii::$app->field->setSearchFilter( $query, Loan::TYPE, $this );
        }

        return $dataProvider;
    }

    /**
     * @deprecated
     */
    private function sourceConsumerLoan() {
        return [ 3, 4, 1752, 1448, 'Patēriņa kredīts', 'Потребительский кредит' ];
    }

    /**
     * @deprecated
     */
    private function sourceRefinanceLoan() {
        return [ 11, 12, 'Aizdevumu refinansēšana', 'Рефинансирование кредитов' ];
    }

    /**
     * @deprecated
     */
    private function sourceBusinessLoan() {
        return [ 9, 10, 'Kredīts uzņēmējdarbībai', 'Кредит для бизнеса' ];
    }

    /**
     * @deprecated
     */
    private function sourceRealEstate() {
        return [ 7, 8, 1757, 1454, 'Kredīts pret nekustamo īpašumu', 'Кредит под залог недвижимости' ];
    }

    /**
     * @deprecated
     */
    private function sourceOnlineLoan() {
        return [ 13, 14, 1765, 1766, 'Online aizdevums līdz 1500 eiro', 'Онлайн кредит до 1500 евро' ];
    }

    /**
     * @deprecated
     */
    private function sourceAutoLoan() {
        return [ 5, 6, 1754, 1452 , 'Kredīts pret automašīnas ķīlu', 'Кредит под залог автомобиля'];
    }

    private function sourceId( $source ) {
        return [ 'source' => $source ];
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    private function getWhereStatement()
    {
        $sourceArray = [];

        foreach (Yii::$app->user->identity->provider->sources as $source) {
            $sourceArray = array_merge(
                $sourceArray,
                ArrayHelper::getValue($source, function ($source) {
                    return [$source->id, $source->wordpress_id, $source->name];
                })
            );
        }

        if (!empty($sourceArray)) {
            return $this->sourceId($sourceArray);
        }

        return true;
    }

    /**
     * @deprecated
     * @return array|bool
     */
    private function oldGetWhereStatement() {
        switch ( Yii::$app->user->identity->provider_id ) {
            case 11:  // Nordic Finance
                $source = $this->sourceConsumerLoan();
                $source = array_merge( $source, $this->sourceAutoLoan() );
                $where = $this->sourceId( $source );
                break;
            case 16:  // Monenza
                $source = $this->sourceConsumerLoan();
                $source = array_merge( $source, $this->sourceOnlineLoan() );
                $where = $this->sourceId( $source );
                break;
            case 15:  // Square+
                $where = $this->sourceId( $this->sourceRealEstate() );
                break;
            case 10:  // Saules kredīts
                $source = $this->sourceConsumerLoan();
                $source = array_merge( $source, $this->sourceOnlineLoan(), $this->sourceAutoLoan() );
                $where  = $this->sourceId( $source );
                break;
            case 17:  // Nordcard
                $source = $this->sourceConsumerLoan();
                $source = array_merge( $source, $this->sourceOnlineLoan(), $this->sourceRefinanceLoan() );
                $where = $this->sourceId( $source );
                break;
            case 18:  // Ūnijas Auto
                $source = $this->sourceAutoLoan();
                $where = $this->sourceId( $source );
                break;
            case 19:  // Inbank
                $source = $this->sourceAutoLoan();
                $where = $this->sourceId( $source );
                break;
            case 20:  // AUTOLIZING.LV
                $source = $this->sourceAutoLoan();
                $where = $this->sourceId( $source );
                break;
            default:
                $where = null;
                break;
        }

        if ( $where ) {
            return $where;
        }

        return true;
    }

    private function needSieveLoansByAmount() {
        if ( Yii::$app->user->identity->provider_id === 11 ) { // Nordic finance
            return true;
        }

        return false;
    }

    public function searchBankLoans( $params ) {
        $query = Loan::find()->where( $this->getWhereStatement() );

        if ( $this->needSieveLoansByAmount() ) {
            $query->andWhere( [ '>=', 'amount', 5000 ] );
        }

        if (Yii::$app->user->identity->status == 1 && Yii::$app->user->identity->activation_time != null) {
            $query->andWhere( [ '>=', 'loan.create_time', Yii::$app->user->identity->activation_time ] );
        }

        $query->joinWith( "person" );

        $dataProvider = new ActiveDataProvider( [
            'query' => $query,
            'sort'  => [ 'defaultOrder' => [ 'id' => SORT_DESC ] ],
        ] );

        $this->sortAttributesAlfabetically( $dataProvider, [
            'person.name',
            'person.personal_code',
            'person.email',
            'status',
            'person.create_time',
            'person.surname'
        ] );
        $this->load( $params );

        $statusAttribute        = $this->getAttribute( 'status' );
        $statusKeyFromAttribute = $this->array_find( $statusAttribute, Loan::getStatuses() );

        $sourceAttribute        = $this->getAttribute( 'source' );
        $sourceKeyFromAttribute = $this->array_find( $statusAttribute, Loan::getStatuses() );

        $query->andFilterWhere( [ 'LIKE', 'person.name', $this->getAttribute( 'person.name' ) ] )
            ->andFilterWhere( [ 'LIKE', 'person.phone', $this->getAttribute( 'person.phone' ) ] )
            ->andFilterWhere( [ 'LIKE', 'person.surname', $this->getAttribute( 'person.surname' ) ] )
            ->andFilterWhere( [ 'LIKE', 'person.personal_code', $this->getAttribute( 'person.personal_code' ) ] )
//              ->andFilterWhere( [ 'LIKE', 'person.create_time', $this->getAttribute( 'person.create_time' ) ] )
            ->andFilterWhere( [ 'LIKE', 'person.email', $this->getAttribute( 'person.email' ) ] )
            ->andFilterWhere( [ 'LIKE', 'amount', $this->getAttribute( 'amount' ) ] )
            ->andFilterWhere( [ 'LIKE', 'person_id', $this->getAttribute( 'person_id' ) ] )
            ->andFilterWhere( [ 'LIKE', 'status', $statusKeyFromAttribute ] );

        if ($this->getAttribute( 'person.create_time' )) {
            $query->andFilterWhere(['between', 'person.create_time', strtotime("midnight", strtotime($this->getAttribute( 'person.create_time' ))), strtotime("tomorrow", strtotime($this->getAttribute( 'person.create_time' ))) - 1]);
        }

        if (isset($params) && $params['LoanSearch'] && $params['LoanSearch']['source']) {
            if (in_array($params['LoanSearch']['source'], $this->sourceRealEstate())) {
                $query->andFilterWhere(['IN', 'source', $this->sourceRealEstate()]);
            } else if (in_array($params['LoanSearch']['source'], $this->sourceOnlineLoan())) {
                $query->andFilterWhere( [ 'IN', 'source', $this->sourceOnlineLoan() ] );
            } else if (in_array($params['LoanSearch']['source'], $this->sourceAutoLoan())) {
                $query->andFilterWhere( [ 'IN', 'source', $this->sourceAutoLoan() ] );
            } else if (in_array($params['LoanSearch']['source'], $this->sourceConsumerLoan())) {
                $query->andFilterWhere( [ 'IN', 'source', $this->sourceConsumerLoan() ] );
            } else if (in_array($params['LoanSearch']['source'], $this->sourceRefinanceLoan())) {
                $query->andFilterWhere( [ 'IN', 'source', $this->sourceRefinanceLoan() ] );
            } else if (in_array($params['LoanSearch']['source'], $this->sourceBusinessLoan())) {
                $query->andFilterWhere( [ 'IN', 'source', $this->sourceBusinessLoan() ] );
            } else {
                $query->andFilterWhere( [ 'LIKE', 'source', $this->getAttribute( 'source' ) ] );
            }
        }

        return $dataProvider;
    }

    private function array_find( $needle, array $haystack ) {
        foreach ( $haystack as $key => $value ) {
            if ( false !== stripos( $value, (string)$needle ) ) {
                return $key;
            }
        }

        return false;
    }

    private function sortAttributesAlfabetically( $dataProvider, $names ) {
        foreach ( $names as $name ) {

            $dataProvider->sort->attributes[ $name ] = [
                'asc'  => [ $name => SORT_ASC ],
                'desc' => [ $name => SORT_DESC ],
            ];
        }
    }
}
