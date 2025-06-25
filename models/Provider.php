<?php

namespace app\models;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "provider".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property SourceProvider[] $sourceProviders
 * @property Source[] $sources
 * @property Source[] $sourceIds
 */
class Provider extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provider';
    }

    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class'     => SaveRelationsBehavior::class,
                'relations' => [
                    'sources'  => ['cascadeDelete' => true],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['sources', 'sourceIds', 'enable'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/provider', 'ID'),
            'name' => Yii::t('app/provider', 'Name'),
            'sourceIds' => Yii::t('app/provider', 'Source Ids'),
            'enable' => Yii::t('app/provider', 'Enable'),
        ];
    }

    /**
     * @return array
     */
    public function getSourceIds(): array
    {
        return ArrayHelper::map($this->sources, 'id', 'id');
    }

    /**
     * @param $ids
     * @return void
     */
    public function setSourceIds($ids): void
    {
        $this->sourceIds = $ids;
    }

    /**
     * @return array
     */
    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * Gets query for [[SourceProviders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSourceProviders()
    {
        return $this->hasMany(SourceProvider::class, ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[Sources]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSources()
    {
        return $this->hasMany(Source::class, ['id' => 'source_id'])->viaTable('source_provider', ['provider_id' => 'id']);
    }
}
