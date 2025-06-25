<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Person;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Loan;
use yii\widgets\ActiveForm;

use app\models\FieldView;
use app\assets\ChangesAsset;
use app\assets\RejectAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Person */

$this->title = $model->name. " ".$model->surname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/person', 'People'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->field->setSearchModel($model);
ChangesAsset::register($this);
RejectAsset::register($this);
?>
<div class="person-view">

    <h1><?= Html::encode($this->title) ?></h1>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-7 col-lg-5">
			 <?php if ( $model->loan->loan_type == 1): ?>
                <h2><?= Yii::t("app/person", "LEGAL PERSON DATA") ?></h2>
            <?php else: ?>
                <h2><?= Yii::t("app/person", "PERSON DATA") ?></h2>
            <?php endif; ?>
		    <?= DetailView::widget([
		        'model' => $model,
		        'attributes' => Yii::$app->field->getFields(Person::TYPE, FieldView::VIEW_PERSON_VIEW, true, Person::TYPE),
		    ]) ?>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
		    <h2><?= Yii::t("app/person", "Notes") ?></h2>
		    <?php $form = ActiveForm::begin(); ?>
		
		    <?= $form->field($model, 'description')->textarea(["rows" => 3]) ?>
		    		
			<div class="form-group">
				<?= Html::submitButton($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
			</div>
		
		    <?php ActiveForm::end(); ?>
		    
			<h2><?= \Yii::t("app/changes", "CHANGES MADE") ?></h2>
			<div id="changes" data-type="<?= Person::TYPE ?>" data-type-id="<?= $model->id ?>" data-offset="0" data-offsetdef="<?= \Yii::$app->params["changes_limit"] ?>" data-url="<?= Url::to(["changes/index"]) ?>">
				<img src="<?= \Yii::$app->urlManager->createUrl("images/loading.gif") ?>" class="loading" alt="Loading">
				<div class="list-group">
				</div>
			</div>
			<a id="more-changes" class="btn btn-block btn-primary hide"><?= Yii::t("app/loan", "Load more changes") ?></a>
			</div>	    
		</div>
	</div>
	
    <p class="fixed-btn">
   		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#w2"><?= Yii::t("app/loan", "Create loan") ?></button>
    	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#w4"><?= Yii::t("app/person", "Edit person") ?></button>
    </p>
    
    <?= $this->render("/loan/_form_create", ["modelPerson" => $model, 'model' => new Loan(), 'absolute' => true]) ?>
    <?= $this->render("_form_edit", ["model" => $model]) ?>
        
    
	<?= $this->render("/loan/_table", [
			"searchModel" => $searchModel,
			"dataProvider" => $dataProvider,
	]) ?>
</div>
