<?php
namespace app\components;

use yii\base\InvalidConfigException;
use himiklab\sortablegrid\SortableGridBehavior;
use app\models\FieldView;
use app\models\Field;
use yii\db\ActiveRecord;


class SortableGridBehaviorExtanded extends SortableGridBehavior
{
	/** @var string database field name for row sorting */
	public $sortableAttribute = 'sortOrder';
	
	public function events()
	{
		return [ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert'];
	}
	
	public function gridSort($items)
	{
		/** @var ActiveRecord $model */
		$model = $this->owner;
		if (!$model->hasAttribute($this->sortableAttribute)) {
			throw new InvalidConfigException("Model does not have sortable attribute `{$this->sortableAttribute}`.");
		}
		
		$newOrder = [];
		$models = [];
		foreach ($items as $old => $new) {
			$fieldNew = Field::findOne($new);
			$fieldViewNew = FieldView::find()->where(["field_id" => $fieldNew->id, "view" => \Yii::$app->getSession()->get("field-order-id")])->one();
			
			if (!$fieldViewNew) {
				$fieldViewNew = new FieldView();
				$fieldViewNew->field_id = $fieldNew->id;
				$fieldViewNew->view = \Yii::$app->getSession()->get("field-order-id");
				$fieldViewNew->save();
			}
			
			$models[$new] = $fieldViewNew;
			$newOrder[$old] = $models[$new]->{$this->sortableAttribute};
		}
		$model::getDb()->transaction(function () use ($models, $newOrder) {
			foreach ($newOrder as $modelId => $orderValue) {
				/** @var ActiveRecord[] $models */
				$models[$modelId]->updateAttributes([$this->sortableAttribute => $orderValue]);
			}
		});
		
		\Yii::$app->cache->flush();
	}
	

	public function beforeInsert()
	{
		/** @var ActiveRecord $model */
		$model = $this->owner;
		if (!$model->hasAttribute($this->sortableAttribute)) {
			throw new InvalidConfigException("Invalid sortable attribute `{$this->sortableAttribute}`.");
		}
	
		$maxOrder = $model->find()->where(["view" => \Yii::$app->getSession()->get("field-order-id")])->max($model->tableName() . '.' . $this->sortableAttribute);
		if ($maxOrder == 0) {
			$prefix = ($model->view * 100);
		} else {
			$prefix = 0;
		}
		$model->{$this->sortableAttribute} = $prefix + $maxOrder + 1;
	}
}