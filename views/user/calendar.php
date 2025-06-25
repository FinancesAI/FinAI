<?php

use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\base\Widget;
use yii\helpers\Url;
use app\assets\CalendarAsset;

/* @var $this yii\web\View */
/* @var $calendar app\models\UserCalendar */

$this->title = Yii::t('app/user', 'Schedule');
$this->params['breadcrumbs'][] = $this->title;
CalendarAsset::register($this);
?>
<div class="user-calendar">

    <h1><a href="https://calendar.google.com/" target="_blank"><?= Html::encode($this->title) ?></a></h1>

	<?= \yii2fullcalendar\yii2fullcalendar::widget(array(
		"id" => "calendar"
	));
	?>
	<iframe src="https://calendar.google.com/calendar/embed?showCalendars=0&amp;showTz=0&amp;height=600&amp;wkst=2&amp;hl=lv&amp;bgcolor=%23FFFFFF&amp;src=info%40onefinance.lv&amp;color=%2323164E&amp;ctz=Europe%2FRiga" style="border-width:0" width="100%" height="600" frameborder="0" scrolling="no"></iframe>	
</div>
<script>
var defaultDate = "<?= date("Y-m-d") ?>"; //'2016-01-12'
var userEvents = [<?php 
	foreach (Yii::$app->getUser()->getIdentity()->getCalendars()->all() as $cal) {
		echo "{title: '{$cal->title}',id: '{$cal->id}',start: '".date("Y-m-d", $cal->start)."',end: '".date("Y-m-d", $cal->end)."'},";
	}
?>];
</script>
