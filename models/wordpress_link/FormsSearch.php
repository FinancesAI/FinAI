<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use app\components\WordpressLink;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is base model class for WordpressLink form submissions.
 */
class FormsSearch extends Model
{
    use WordpressLinkDbTrait;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = WsfForm::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /**
         * @var WordpressLink $wp
         */
        $wp = Yii::$app->get('wordpressLink');

        // grid filtering conditions
//        $query->andWhere([
//            'in',
//            'id',
//            $wp->forms,
//        ]);

        return $dataProvider;
    }
}
