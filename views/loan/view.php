<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Loan;
use app\models\Person;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\FieldView;
use yii\bootstrap\Tabs;
use app\assets\ChangesAsset;
use app\components\api\ApiInit;
use app\models\LoanExtra;
use yii\bootstrap\ButtonGroup;
use vakorovin\datetimepicker\Datetimepicker;
use app\models\LoanProgerss;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */
/* @var $form yii\widgets\ActiveForm */

if ($model->loan_type == 1) {
    $this->title = $model->id." - ".$model->person->company_name." - ".$model->person->registration_number;
} else {
    $this->title = $model->id." - ".$model->person->name." ".$model->person->surname." - ".$model->person->personal_code;
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/loan', 'Loans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
ChangesAsset::register($this);
?>
<script>
	app.loan = {
		id: "<?= $model->id ?>",
	};
</script>
<div class="loan-view">
<style>
.tooltip.fade.bottom
{
  left:80px !important;
}
</style>
	<?php
	$model->setRatingText();
	if ($model->rating == 1) {
		?><h1 class="priority" data-toggle="tooltip" data-placement="bottom" title="<?= $model->ratingText ?>"><?= Html::encode($this->title) ?></h1><?php
	} else {
		if ($model->rating == -1) {
			?><h1 class="not-priority" data-toggle="tooltip" data-placement="bottom" title="<?= $model->ratingText?>"><?= Html::encode($this->title) ?></h1><?php
		} else {
			?><h1 class=""><?= Html::encode($this->title) ?></h1><?php
		}
	}
	?>

    <p class="fixed-btn">
		<button type="button" class="btn <?php if ($model->appointment) : ?>btn-success<?php else: ?>btn-primary<?php endif; ?>" id="set-app"><?php if ($model->appointment) : ?><?= Yii::t("app/loan", "Edit appointment") ?><?php else: ?><?= Yii::t("app/loan", "Add appointment") ?><?php endif; ?></span></button>
		<button type="button" class="btn btn-primary" id="set-rem"><span class="glyphicon glyphicon-time"></span></button>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#l"><?= Yii::t("app/loan", "Edit loan") ?></button>
		<?php if ($model->extra) { ?>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#le"><?= Yii::t("app/loan", "Edit extra") ?></button>
	    <?php } ?>
	   	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#lp"><?= Yii::t("app/loan", "Edit person") ?></button>
	   	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#lg"><?= Yii::t("app/loan", "Edit guarantor") ?></button>
    </p>

	<?= $this->render("_form_edit_person", ["model" => $model->person, "lid" => $model->id]) ?>
	<?= $this->render("_form_edit", ["model" => $model, "modelPerson" => $model->person]) ?>

	<?= $this->render("_form_edit_guarantor", ["model" => $model]) ?>

	<?php if ($model->extra) { ?>
	<?= $this->render("_form_edit_extra", ["model" => $model]) ?>
    <?php } ?>
    <div class="loan-app-time-fx hide">
		<?php $form = ActiveForm::begin(); ?>
		<hr/>
		<div class="row">
	    	<div class="col-xs-12 col-md-12">
				<?php
                echo "<div class='form-group'>";
                echo $form->field($model, "reminder_time")->widget(Datetimepicker::classname(), [
                    'options' => [
                        "format" => "unixtime",
                        "class" => "appointment-datetime",
                        "id" => "custom-app-time-val",
                        "value" => "",
                        "placeholder" => ($model->appointment ? Yii::$app->formatter->asDate(
                            $model->appointment->date
                        ) : Yii::t("app/loan", "Set appointment date.")),
                        "data-placeholder" => Yii::t("app/loan", "Set appointment date.")
                    ]
                ])->label(false);
                echo "<span class='appointment-rm glyphicon glyphicon-trash text-danger'><span class='hide'>" . $model->id . "</span></span>";
                echo "</div>";
				?>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

	<div class="loan-rm-time-fx hide">
		<?php $form = ActiveForm::begin(); ?>
		<hr/>
		<div class="row">
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', '30 min'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm30', 'data' => ["time" => strtotime("+30 minutes"), "cl" => "1"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', '1 hour'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("+1 hour"), "cl" => "1"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', '2 hours'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("+2 hours"), "cl" => "1"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', '4 hours'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("+4 hours"), "cl" => "2"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-2">
				<?= Html::a(Yii::t('app/loan', '8 hours'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("+8 hours"), "cl" => "2"]]) ?>
			</div>

			<div class="clearfix mb5"></div>

			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', 'Mon'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm30', 'data' => ["time" => strtotime("11:00", strtotime("monday")), "cl" => "3"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', 'Tue'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("11:00", strtotime("tuesday")), "cl" => "3"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', 'Wed'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("11:00", strtotime("wednesday")), "cl" => "3"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-1">
				<?= Html::a(Yii::t('app/loan', 'Thu'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("11:00", strtotime("thursday")), "cl" => "3"]]) ?>
			</div>
			<div class="col-xs-6 col-md-2 col-2">
				<?= Html::a(Yii::t('app/loan', 'Fri'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm2', 'data' => ["time" => strtotime("11:00", strtotime("friday")), "cl" => "3"]]) ?>
			</div>

			<div class="clearfix mb5"></div>
			<div class="col-xs-6 col-md-4 col-1">
				<?= Html::a(Yii::t('app/loan', 'Next day'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rm24', 'data' => ["time" => strtotime("11:00", strtotime("+1 day")), "cl" => "3"]]) ?>
			</div>
			<div class="col-xs-6 col-md-4 col-1">
				<?= Html::a(Yii::t('app/loan', 'Next monday'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rmmd', 'data' => ["time" => strtotime("11:00", strtotime("next monday")), "cl" => "4"]]) ?>
			</div>
			<div class="col-xs-6 col-md-4 col-2">
				<?= Html::a(Yii::t('app/loan', 'Next tuesday'), "", ['class' => 'btn btn-primary btn-block btn-rm', 'id' => 'rmmd', 'data' => ["time" => strtotime("11:00", strtotime("next tuesday")), "cl" => "4"]]) ?>
			</div>
	    	<div class="col-xs-12 col-md-12">
				<?php
				echo "<div class='form-group'>";
				echo $form->field($model, "reminder_time")->widget(Datetimepicker::classname(), [
						'options' => ["format" => "unixtime", "class" => "", "id" => "custom-rm-time-val", "value" => "", "placeholder" => ($model->reminder_time ? Yii::$app->formatter->asDate($model->reminder_time) : "Set custom time.")]
				])->label(false);
				echo "</div>";
				?>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

    <?php Yii::$app->field->setSearchModel($model) ?>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="loan-progress-bar" class="btn-group btn-group-justified" role="group" aria-label="...">
			<?php
			$progresses = $model->getProgressFormated();
			foreach ($model->getInProgress() as $progressId => $progressTitle) {
				if ($progressId == 0) {
					continue;
				}
				$progressVal = "";
				$progressTxtVal = "";
				$progressClass = "";
				$progressAm = "";
				if (isset($progresses[$progressId])) {
					$progressData = $progresses[$progressId];
					$progressClass = $progressData->getBarClass();
					$progressVal = $progressData->status;
					$progressAm = $progressData->amount;
					$progressTxtVal = $progressData->text;
				} else {
					$progressClass = " btn-default";
				}

				?><div class="btn-group" role="group">
                    <div class="dropdown">
                        <button id="dLabel" type="button" onclick="return false;" data-toggle="dropdown" class="in-progress-loan btn<?= $progressClass ?>" data-id="<?= $progressId ?>"><?= $progressTitle ?></button>
                        <div class="dropdown-menu">
                            <label>Status</label>
                            <?= Html::dropDownList("prog", $progressVal, LoanProgerss::getStatuses(), ["class" => "form-control prog-status"]) ?>
                            <label>Approved amount</label>
                            <input type="text" name="prog-val" value="<?= $progressAm ?>" class="form-control prog-val">
                            <label>Comment</label>
                            <textarea class="form-control prog-txt" ><?= $progressTxtVal ?></textarea>
                            <button class="btn btn-primary btn-block btn-prog-save" data-provider="<?= $progressId ?>">Save</button>
                        </div>
                    </div>
				</div><?php
			}
			?>
			</div>
		</div>
	</div>

	<p></p>
		<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 rd">
			    <?php if ($model->loan_type == 1): ?>
			        <h2><?= Yii::t("app/loan", "LEGAL PERSON CREDIT DATA") ?></h2>
			    <?php else: ?>
			        <h2><?= Yii::t("app/loan", "CREDIT DATA") ?></h2>
			    <?php endif; ?>
					    <?= DetailView::widget([
					        'model' => $model,
					        'attributes' => Yii::$app->field->getFields(Loan::TYPE, FieldView::VIEW_LOAN_VIEW, true, false)
					    ]) ?>
				</div>
					<!-- loan end -->
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 rd">
				 <?php Yii::$app->field->setSearchModel($model->person) ?>
				 <?php if ($model->loan_type == 1): ?>
			           <h2><?= Yii::t("app/person", "LEGAL PERSON DATA") ?> &nbsp;<small><a href="<?= Url::toRoute(["person/view", "id" => $model->person_id]) ?>"><?= Yii::t("app/loan", "MORE INFORMATION") ?></a></small></h2>
				    <?php else: ?>
				        <h2><?= Yii::t("app/person", "PERSON DATA") ?><small>&nbsp;<a href="<?= Url::toRoute(["person/view", "id" => $model->person_id]) ?>"><?= Yii::t("app/loan", "MORE INFORMATION") ?></a></small></h2>
				    <?php endif; ?>	 
			    <?= DetailView::widget([
			        'model' => $model->person,
			        'attributes' => Yii::$app->field->getFields(Person::TYPE, FieldView::VIEW_PERSON_VIEW, true, Person::TYPE),
			    ]) ?>
			</div>

				<!-- person end -->
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 rd">
						<?php if ($model->extra) { ?>
					    <h2><?= Yii::t("app/loan", "Loan extra data") ?></h2>
					    <?= DetailView::widget([
					        'model' => $model->extra,
					        'attributes' => [
					        	[
						            'attribute' => 'property_address',
						            'value' => $model->extra->property_address,
					        		'visible' => (boolean)$model->extra->property_address,
					        	],
					        	[
						            'attribute' => 'car_description',
						            'value' => $model->extra->car_description,
					        		'visible' => (boolean)$model->extra->car_description,
						        ],
					        	[
						            'attribute' => 'car_phone',
						            'value' => $model->extra->car_phone,
						            'visible' => (boolean)$model->extra->car_phone,
					        	],
					        	[
						            'attribute' => 'car_owner',
						            'value' => $model->extra->car_owner,
						            'visible' => (boolean)$model->extra->car_owner,
					        	],
					        	[
					        		'attribute' => 'car_owner_address',
					        		'value' => $model->extra->car_owner_address,
					        		'visible' => (boolean)$model->extra->car_owner_address,
					        	],
					        	[
					        		'attribute' => 'car_owner_declared_address',
					        		'value' => $model->extra->car_owner_declared_address,
					        		'visible' => (boolean)$model->extra->car_owner_declared_address,
					        	],
					        	[
					        		'attribute' => 'car_workplace',
					        		'value' => $model->extra->car_workplace,
					        		'visible' => (boolean)$model->extra->car_workplace,
					        	],
					        	[
					        		'attribute' => 'car_position',
					        		'value' => $model->extra->car_position,
					        		'visible' => (boolean)$model->extra->car_position,
					        	],
					        	[
					        		'attribute' => 'car_work_experience',
					        		'value' => $model->extra->car_work_experience,
					        		'visible' => (boolean)$model->extra->car_work_experience,
					        	],
					        	[
					        		'attribute' => Yii::t('app/loan', 'files'),
					        		'value' =>  (($model->extra->vsaa_statement_ep52 || $model->extra->vsaa_statement || $model->extra->bank_account_statement) ? Yii::t('app/loan', 'Yes') : Yii::t('app/loan', 'No')),
					        		'format' => 'raw',
					        	],
					        	[
					        		'attribute' => 'document_type',
					        		'value' => $model->extra->document_type,
					        		'visible' => (boolean)$model->extra->document_type,
					        	],
					        	[
					        		'attribute' => 'document_nr',
					        		'value' => $model->extra->document_nr,
					        		'visible' => (boolean)$model->extra->document_nr,
					        	],
					        	[
					        		'attribute' => 'document_expire',
					        		'value' => $model->extra->document_expire,
					        		'visible' => (boolean)$model->extra->document_expire,
					        	],
					        	[
					        		'attribute' => 'bank_account_nr',
					        		'value' => $model->extra->bank_account_nr,
					        		'visible' => (boolean)$model->extra->bank_account_nr,
					        	],
					        	[
					        		'attribute' => 'credit_data',
					        		'value' => $model->extra->credit_data,
					        		'visible' => (boolean)$model->extra->credit_data,
					        	],
					        	[
					        		'attribute' => 'api_status_description',
					        		'value' => $model->extra->api_status_description,
					        		'visible' => (boolean)$model->extra->api_status_description,
					        	],
					        	[
					        		'attribute' => 'api_status_description_aizdevums',
					        		'value' => $model->extra->getAizdevumsDesc(),
					        		'visible' => (boolean)$model->extra->getAizdevumsDesc(),
					        	],
					        	[
					        		'attribute' => 'api_status_description_efinance',
					        		'value' => $model->extra->getEfinanceDesc(),
					        		'visible' => (boolean)$model->extra->getEfinanceDesc(),
					        	],
					        ]
					    ]) ?>
					    <?php } ?>

					    <?php if ($model->enterprise) { ?>
					    <h2><?= Yii::t("app/loan", "Loan enterprise data") ?></h2>
					    <?= DetailView::widget([
					        'model' => $model->enterprise,
					        'attributes' => [
					        	[
						            'attribute' => 'name',
						            'value' => $model->enterprise->name,
					        		'visible' => (boolean)$model->enterprise->name,
					        	],
					        	[
					        		'attribute' => 'nr',
					        		'value' => $model->enterprise->nr,
					        		'visible' => (boolean)$model->enterprise->nr,
					        	],
					        	[
					        		'attribute' => 'address_actual',
					        		'value' => $model->enterprise->address_actual,
					        		'visible' => (boolean)$model->enterprise->address_actual,
					        	],
					        	[
					        		'attribute' => 'address_domicile',
					        		'value' => $model->enterprise->address_domicile,
					        		'visible' => (boolean)$model->enterprise->address_domicile,
					        	],
					        	[
					        		'attribute' => 'bank_account_statement',
					        		'value' => "<a href=\"https://www.server.lv/?file=".str_replace("$", "%24", $model->enterprise->bank_account_statement)."&f_preview=yes&fn=".str_replace("-", "", $model->person->personal_code)."-bank-enteprise\" target=\"_blank\">Open bank account statement</a>",
					        		'format' => 'raw',
					        		'visible' => (boolean)$model->enterprise->bank_account_statement,
					        	],
					        ]
					    ]) ?>
					    <?php } ?>

					    <?php if ($model->guarantor) { ?>
					    <h2><?= Yii::t("app/loan", "Loan guarantor data") ?></h2>
					    <?= DetailView::widget([
					        'model' => $model->guarantor,
					        'attributes' => [
					        	[
						            'attribute' => 'full_name',
						            'value' => $model->guarantor->full_name,
					        		'visible' => (boolean)$model->guarantor->full_name,
					        	],
					        	[
						            'attribute' => 'personal_code',
						            'value' => $model->guarantor->personal_code,
					        		'visible' => (boolean)$model->guarantor->personal_code,
					        	],
					        	[
					        		'attribute' => 'phone',
					        		'value' => $model->guarantor->phone,
					        		'visible' => (boolean)$model->guarantor->phone,
					        	],
					        	[
			        				'attribute' => 'email',
			        				'value' => $model->guarantor->email,
			        				'visible' => (boolean)$model->guarantor->email,
					        	],
					        	[
					        		'attribute' => 'address',
					        		'value' => $model->guarantor->address,
					        		'visible' => (boolean)$model->guarantor->address,
					        	],
					        	[
			        				'attribute' => 'postcode',
			        				'value' => $model->guarantor->postcode,
			        				'visible' => (boolean)$model->guarantor->postcode,
					        	],
					        	[
						            'attribute' => 'workplace',
						            'value' => $model->guarantor->workplace,
					        		'visible' => (boolean)$model->guarantor->workplace,
					        	],
					        	[
						            'attribute' => 'workposition',
						            'value' => $model->guarantor->workposition,
					        		'visible' => (boolean)$model->guarantor->workposition,
					        	],
					        	[
						            'attribute' => 'work_experience',
						            'value' => $model->guarantor->work_experience." m.",
					        		'visible' => (boolean)$model->guarantor->work_experience,
					        	],
					        	[
						            'attribute' => 'income',
						            'value' => $model->guarantor->income." €",
					        		'visible' => (boolean)$model->guarantor->income,
					        	],
					        	[
						            'attribute' => 'outcome',
						            'value' => $model->guarantor->outcome." €",
					        		'visible' => (boolean)$model->guarantor->outcome
					        	],
					        	[
					        		'attribute' => 'bank_statement',
					        		'value' => "<a href=\"https://www.server.lv/?file=".str_replace("$", "%24", $model->guarantor->bank_statement)."&f_preview=yes\" target=\"_blank\">Open bank account statement</a>",
					        		'format' => 'raw',
					        		'visible' => (boolean)$model->guarantor->bank_statement,
					        	],
					        ]
					    ]) ?>
					    <?php } ?>
					      <?php $form = ActiveForm::begin(); ?>
			            <div class="form-group">
			                <?= $form->field($model, 'comment')->textarea([
			                    'rows' => 4
			                   
			                ])->label(Yii::t("app/loan", "COMMENT")) ?>
			            </div>
			        <?php ActiveForm::end(); ?>
					</div>
					
				<!-- addition info add -->
		</div>

	<div class="row">	
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 rd">
		   <h2><?= \Yii::t("app/changes", "Notes history") ?></h2>
			<div id="changes-desc" data-type="<?= Loan::TYPE ?>" data-type-id="<?= $model->id ?>" data-offset="0" data-offsetdef="<?= \Yii::$app->params["changes_limit"] ?>" data-url="<?= Url::to(["changes/index", "desc" => "1"]) ?>">
				<img src="<?= \Yii::$app->urlManager->createUrl("images/loading.gif") ?>" class="loading" alt="Loading">
				<div class="list-group">
				</div>
			</div>
			<a id="more-changes-desc" class="btn btn-block btn-primary hide"><?= Yii::t("app/loan", "Load more description changes") ?></a>

			<h2><?= \Yii::t("app/changes", "Logs history") ?></h2>
			<div id="changes" data-type="<?= Loan::TYPE ?>,<?= LoanExtra::TYPE ?>,<?= LoanProgerss::TYPE ?>" data-type-id="<?= $model->id ?>" data-offset="0" data-offsetdef="<?= \Yii::$app->params["changes_limit"] ?>" data-url="<?= Url::to(["changes/index"]) ?>">
				<img src="<?= \Yii::$app->urlManager->createUrl("images/loading.gif") ?>" class="loading" alt="Loading">
				<div class="list-group">
				</div>
			</div>
			<a id="more-changes" class="btn btn-block btn-primary hide" style="margin-bottom: 20px;"><?= Yii::t("app/loan", "Load more changes") ?></a>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 rd">
		  		<?= Tabs::widget([
				'options' => ["class" => "nav nav-tabs nav-justified"],
			    'items' => [
			        [
			            'label' => Yii::t("app/loan", "Notes"),
			            'content' => $this->render("_tab_notes", ["model" => $model]),
			            'active' => true
			        ],
			        [
			            'label' => Yii::t("app/loan", "Send email/sms"),
			            'content' => $this->render("_tab_send", ["model" => $model]),
			        ],
			        [
			            'label' => Yii::t("app/loan", "API"),
			            'items' => (new ApiInit())->run($model),
			        ],
			    	[
			            'label' => Yii::t("app/loan", "Files"),
			            'content' => $this->render("_tab_files", ["model" => $model]),
			        ],
			    	[
			            'label' => Yii::t("app/loan", "Others"),
			            'content' => $this->render("_tab_others", ["model" => $model]),
			        ],
			    ],
			]); ?>
		</div>
	</div>
</div>
