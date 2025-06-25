<?php
/**
 * @see app\controllers\WordpressLinkController::actionSubmissions()
 * @author Nils <deele@tuta.io>
 */

use app\models\wordpress_link\FormSubmissionsSearch;
use app\models\wordpress_link\WPFormSubmissionInterface;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var string $tableClassName
 * @var ActiveDataProvider $dataProvider
 * @var FormSubmissionsSearch $searchModel
 * @var int $formId
 * @var string $formName
 * @var bool $isActive
 */
?>
<div class="wordpress-link wordpress-link_submissions">
    <div class="h1"><?= Yii::t(
            'app/wordpress-link',
            'WordPress Link'
        ) ?></div>
    <?= $this->render( "/admin/_nav" ) ?>
    <div class="container">
        <?= $this->render('_header', [
            'heading' => Yii::t(
                'app/wordpress-link',
                'Submissions'
            ),
            'formId' => $formId,
            'formName' => $formName,
            'isActive' => $isActive,
        ]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => $tableClassName],
            'rowOptions' => static function (WPFormSubmissionInterface $model) {
                $options = [];
                if ($model->getIsSynced()) {
                    $options['class'] = 'success';
                }
                return $options;
            },
            'columns' => [
                'id',
                'date_added',
                [
                    'attribute' => 'status',
                    'value' => static function (WPFormSubmissionInterface $model) {
                        return $model->getIsSynced() ? Yii::t(
                            'app/wordpress-link',
                            'Synced'
                        ) : Yii::t(
                            'app/wordpress-link',
                            'Not yet synced'
                        );
                    }
                ],
            ],
        ]) ?>
    </div>
</div>
