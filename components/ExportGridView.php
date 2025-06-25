<?php 
namespace app\components;

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\search\LoanSearch;
use app\models\Log;
use app\models\Loan;

use app\models\Person;
use app\models\search\PersonSearch;
use app\models\search\BillSearch;
use vakorovin\datetimepicker\Datetimepicker;
use yii\helpers\Json;

class ExportGridView extends GridView {
	public function run() {
		if (\Yii::$app->setting->get("export_role") == \Yii::$app->getUser()->getIdentity()->role)
			if (\Yii::$app->getRequest()->post("export") == true)
				return $this->_export();
		
		parent::run();
		
		if (\Yii::$app->setting->get("export_role") == \Yii::$app->getUser()->getIdentity()->role)
			if (\Yii::$app->getRequest()->post("export") == false)
				echo $this->_model();
	}

	private function _model() {
		$columns = [];

		foreach ($this->columns as $column) {
			if (property_exists($column, "attribute"))
				$columns[] = $column->attribute;
		}
		
		Modal::begin([
				'size' => Modal::SIZE_LARGE,
				'header' => '<h2>'.\Yii::t("app/export", "Export data").'</h2>',
				'toggleButton' => ['label' => \Yii::t("app/export", "Export data"), 'class' => 'btn btn-primary btn-export'],
		]);
		?>
		<div class="form">
		<div class="checkbox"><label><input type="checkbox" id="checkAll" checked> Check/Uncheck</label></div>
		
		<?= Html::beginForm("", "post", ["id" => "checkAllCheck"]) ?>
		<?= Html::hiddenInput("export", "true") ?>
		
		<?php 

		$colPrimary = "";
		$colSecondary = "";

		foreach ($columns as $col) {
			if (!$col) {
				continue;
			}
			
			$colItem = "<div class=\"checkbox\">".Html::checkbox("attr[".str_replace(["person_", "loan_", "loan.count"], ["person.", "loan.", "loan_count"], $col)."]", true, ["label" => $col])."</div>";
				
			if (strpos($col, 'person_') !== false) {
				$colSecondary .= $colItem;
			} else {
				$colPrimary .= $colItem;
			}
		}
		?>
		<div class="row">
			<div class="col-xs-6">
			<?php 
				if ($colPrimary) {
					echo "<h3>Primary data</h3>";
					echo $colPrimary;
				}
			?>
			</div>
			<div class="col-xs-6">
			<?php 
				if ($colSecondary) {
					echo "<h3>Secondary data</h3>";
					echo $colSecondary;
				}
			?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-6">
				<h3>Other options</h3>
				<div class="form-group">
					<div class="checkbox">
					  <label>
					    <input name="headers" type="checkbox" value="1" checked>
					    Rander first row as titles
					  </label>
					</div>
					<div class="radio">
					  <label>
					    <input type="radio" name="format" id="optionsRadios1" value="csv">
					    Format csv
					  </label>
					</div>
					<div class="radio">
					  <label>
					    <input type="radio" name="format" id="optionsRadios2" value="xlsx" checked>
					    Format xlsx
					  </label>
					</div>
					<div class="checkbox">
					  <label>
					    <input name="partners" type="checkbox" value="1">
					    Partner progress status/amount/desc
					  </label>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<h3>Date options</h3>
				<div class="form-group">
					<label>Filter by</label>
					<select name="what" class="form-control">
						<option value="close_time">Close date</option>
						<option value="create_time" selected>Create date</option>
						<option value="update_time">Update date</option>
					</select>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>Date from</label>
							<?= Datetimepicker::widget([
							    'name' => "from",
				    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
							]) ?>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Date to</label>
							<?= Datetimepicker::widget([
							    'name' => "to",
				    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
							]) ?>
						</div>
					</div>
				</div>
				<small>Note: Date filter will be applied to filtered loans.</small>
			</div>
		</div>
		
		<div class="form-group">
			<?= Html::submitInput(\Yii::t("app/export", "Export data"), ["class" => 'btn btn-primary']) ?>
		</div>
		
		<?= Html::endForm() ?>
		</div>
		<?php
		Modal::end();
	}

	/**
	 * @var $data Loan
	 */
	private function _export() {
		\Yii::$app->systemlog->create(Log::TYPE_EXPORT);
		$betweenPrefix = "loan";
		
		$columns = "";
		foreach (\Yii::$app->getRequest()->post("attr") as $attrKey => $attrValue) {
			if ($attrKey == "bill_amount" || $attrKey == "bill_status") {
				continue;
			}
			
			if (strpos($attrKey, 'person.') === false && $this->filterModel->className() == LoanSearch::className()) {
				$columns .= "`loan`.".$attrKey.", ";
			} elseif (strpos($attrKey, 'loan.') === false && $this->filterModel->className() == PersonSearch::className()) {
				$columns .= "`person`.".$attrKey.", ";
			} elseif ($this->filterModel->className() == BillSearch::className()) {
				$columns .= "`bill`.".str_replace("loan.", "loan_", $attrKey).", ";
				$betweenPrefix = "bill";
			} else {
				$columns .= $attrKey.", ";
			}
		}
		
		if ($this->filterModel->className() == LoanSearch::className())
			$columns .= "`loan`.person_id, ";
		
		if ($this->filterModel->className() == PersonSearch::className())
			$columns .= "`person`.id, ";

		$between = null;
		if (\Yii::$app->getRequest()->post("from")) {
			$between = ['>', $betweenPrefix.".".\Yii::$app->getRequest()->post("what"), $this->getFrom()];
		}
		
		if (\Yii::$app->getRequest()->post("to")) {
			$between = ['>', $betweenPrefix.".".\Yii::$app->getRequest()->post("what"), $this->getTo()];
		}
		
		if (\Yii::$app->getRequest()->post("from") && \Yii::$app->getRequest()->post("to")) {
			$between = ['between', $betweenPrefix.".".\Yii::$app->getRequest()->post("what"), $this->getFrom(), $this->getTo()];
		}

		if ($between) {
			if (property_exists($this->dataProvider, "query")) {
				$exportData = $this->dataProvider->query->select($columns)->andFilterWhere($between);
			} else {
				$exportData = $this->dataProvider->solr->select($columns);
				$exportData = $this->dataProvider->solr->setFilterQuery(\Yii::$app->getRequest()->post("what").":[".$this->getFrom()." TO ".$this->getTo()."]");
			}
		} else {
			if (property_exists($this->dataProvider, "query")) {
				$exportData = $this->dataProvider->query->select($columns);
			} else {
				$exportData = $this->dataProvider->solr->select($columns);
			}
		}

		$json = serialize(\Yii::$app->getRequest()->post("attr"));

		if (!file_exists(\Yii::getAlias('@app')."/export_object")) {
			file_put_contents(\Yii::getAlias('@app')."/export_object", serialize($this->dataProvider));
			file_put_contents(\Yii::getAlias('@app')."/export_post", serialize(\Yii::$app->getRequest()->post("attr")));
			$cmd = PHP_BINDIR . '/php ' . \Yii::getAlias('@app') . '/yii loan/export '.(int)\Yii::$app->getRequest()->post("headers").' '.(int)\Yii::$app->getRequest()->post("partners").' '.\Yii::$app->getRequest()->post("format").' '.(int)\Yii::$app->getUser()->getId().' > /dev/null &';
			exec($cmd);
			
			\Yii::$app->getSession()->setFlash("success", "Export process started. You will recive email when export will finish.");
		} else {
			\Yii::$app->getSession()->setFlash("danger", "Some other export in progress. Try later.");
		}
		
		\Yii::$app->getResponse()->redirect(["site/index"]);
		\Yii::$app->end();
	}
	
	public function getFrom() {
		return strtotime("midnight", strtotime(\Yii::$app->getRequest()->post("from")));
	}
	
	public function getTo() {
		return strtotime("tomorrow", strtotime(\Yii::$app->getRequest()->post("to"))) - 1;
	}
}