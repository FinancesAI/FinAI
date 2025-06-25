<?php 
use app\models\Changes;
use yii\bootstrap\Nav;
use app\models\Loan;
use yii\widgets\DetailView;

$u = "https://server.lv/?file=";
$u2 = "&f_preview=yes";
?>

<h2><?= Yii::t("app/loan", "") ?></h2>


    <?php if ($model->extra) { ?>
    <?php if ($model->extra->vsaa_statement || $model->extra->vsaa_statement_ep52 || $model->extra->bank_account_statement) { ?>
    <p class="lead"><?= Yii::t("app/loan", "Active files") ?></p>
    <?= DetailView::widget([
        'model' => $model->extra,
        'attributes' => [
        	[
        		'attribute' => 'vsaa_statement',
        			'value' => "<a href=\"".$u.str_replace("$", "%24", $model->extra->vsaa_statement).$u2."&fn=".str_replace("-", "", $model->person->personal_code)."-vsaa\" target=\"_blank\">".fileTitleSign($model->extra->vsaa_statement, "Open vsaa statement")."</a>",
        		'format' => 'raw',
        		'visible' => (boolean)$model->extra->vsaa_statement,
        	],
        	[
        		'attribute' => 'vsaa_statement_ep52',
        			'value' => "<a href=\"".$u.str_replace("$", "%24", $model->extra->vsaa_statement_ep52).$u2."&fn=".str_replace("-", "", $model->person->personal_code)."-vsaa52\" target=\"_blank\">".fileTitleSign($model->extra->vsaa_statement_ep52, "Open vsaa statement ep52")."</a>",
        		'format' => 'raw',
        		'visible' => (boolean)$model->extra->vsaa_statement_ep52,
        	],
        	[
        		'attribute' => 'bank_account_statement',
        	    'value' => "<a href=\"".$u.str_replace("$", "%24", $model->extra->bank_account_statement).$u2."&fn=".str_replace("-", "", $model->person->personal_code)."-bank\" target=\"_blank\">".fileTitleSign($model->extra->bank_account_statement, "Open bank account statement")."</a>",
        		'format' => 'raw',
        		'visible' => (boolean)$model->extra->bank_account_statement,
        	],
        ]
    ]) ?>
    <?php } ?>
    <?php } ?>

<?php 
$files = Changes::find()->where(["type" => 3, "type_id" => $model->id])->andWhere(["in", "attr", ["vsaa_statement_ep52", "bank_account_statement", "vsaa_statement"]])->orderBy("id desc")->all();
if ($files) {
	?>
	<p class="lead"><?= Yii::t("app/loan", "File history") ?></p>
	<ul class="list-group"><?php
	foreach ($files as $file) {
		$disabled = "";
		$prefix = "";
		$surfix = "";
		
		if ($file->create_time < strtotime('-12 day', time())) {
			$disabled = " disabled text-muted";
			$prefix = "<s>";
			$surfix = "</s>";
		}
		
		$ft = null;
		if ($file->attr == "vsaa_statement_ep52") { $ft = "-vsaa52"; }
		if ($file->attr == "vsaa_statement") { $ft = "-vsaa"; }
		if ($file->attr == "bank_account_statement") { $ft = "-bank"; }
		
		if ($model->extra) {
			if ($file->attr_to == $model->extra->vsaa_statement || $file->attr_to == $model->extra->vsaa_statement_ep52  || $file->attr_to == $model->extra->bank_account_statement ) {
				$prefix = "<b>";
				$surfix = "</b>";
			} else {
			    $ft = $ft."-".$file->id;
			}
		}

		?><li class="list-group-item"><?= $prefix ?><a href="<?= $u ?><?= str_replace("$", "%24", $file->attr_to) ?><?= $u2 ?>&fn=<?= str_replace("-", "", $model->person->personal_code) ?><?= $ft ?>" target="_blank" class="btn text-left btn-block<?= $disabled ?>" style="text-align:left;font-weight: inherit;"><?= fileTitleSign($file->attr_to, Changes::getAttrLabel($file->attr, Loan::TYPE)) ?> <small>(<?= Yii::$app->formatter->asDate($file->create_time) ?>)</small></a><?= $surfix ?></li><?php
	}
	?></ul><?php
} else {
	?><p><?= Yii::t("app/loan", "No files found.") ?></p><?php
}

function fileTitleSign($title, $extraTitle = null) {
	if (strpos($title, 'large') !== false) {
	    if ($extraTitle) {
	    	return $extraTitle." <span class='text-danger glyphicon glyphicon-warning-sign' data-toggle='tooltip' data-placement='top' title='File size more then 1mb'></span>";
	    } else {
	    	return $title." <span class='text-danger glyphicon glyphicon-warning-sign' data-toggle='tooltip' data-placement='top' title='File size more then 1mb'></span>";
	    }
	}
	
	if ($extraTitle) {
		return $extraTitle;
	} else {
		return $title;
	}
}
?>
<p><small><?= Yii::t("app/loan", "Files are active for 12 days after upload.") ?></small></p>

