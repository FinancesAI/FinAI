<?php

namespace app\models;

use himiklab\sortablegrid\SortableGridBehavior;
use Yii;

/**
 * This is the model class for table "source".
 *
 * @property int $id
 * @property int $wordpress_id
 * @property string|null $name
 * @property string|null $lead_event_sid
 * @property string|null $sales_event_sid
 * @property int|null $order
 *
 * @property Provider[] $providers
 * @property SourceProvider[] $sourceProviders
 */
class Source extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'source';
    }

    /**
     * @return \string[][]
     */
    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::class,
                'sortableAttribute' => 'order'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wordpress_id'], 'required'],
            [['wordpress_id', 'order'], 'integer'],
            [['name', 'lead_event_sid', 'sales_event_sid'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/source', 'ID'),
            'wordpress_id' => Yii::t('app/source', 'Wordpress ID'),
            'name' => Yii::t('app/source', 'Name'),
            'lead_event_sid' => Yii::t('app/source', 'Lead Event Sid'),
            'sales_event_sid' => Yii::t('app/source', 'Sales Event Sid'),
            'order' => Yii::t('app/source', 'Order'),
        ];
    }

    /**
     * Gets query for [[Providers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviders()
    {
        return $this->hasMany(Provider::class, ['id' => 'provider_id'])->viaTable('source_provider', ['source_id' => 'id']);
    }

    /**
     * Gets query for [[SourceProviders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSourceProviders()
    {
        return $this->hasMany(SourceProvider::class, ['source_id' => 'id']);
    }
}
