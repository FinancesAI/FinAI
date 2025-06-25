<?php
/**
 * @see app\controllers\WordpressLinkController::actionIndex()
 * @author Nils <deele@tuta.io>
 */

use app\models\wordpress_link\FormsSearch;
use app\models\wordpress_link\WPFormInterface;
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var string $tableClassName
 * @var bool $isOnline
 * @var ActiveDataProvider $dataProvider
 * @var FormsSearch $searchModel
 */
?>
<div class="wordpress-link wordpress-link_index">
    <div class="h1"><?= Yii::t(
            'app/wordpress-link',
            'WordPress Link'
        ) ?></div>
    <?= $this->render("/admin/_nav") ?>
    <?= Alert::widget([
        'options' => [
            'class' => 'alert alert-' . ($isOnline ? 'success' : 'danger'),
        ],
        'body' => Yii::t(
            'app/wordpress-link',
            'Remote DB: {status}',
            [
                'status' => ($isOnline ? Yii::t(
                    'app/wordpress-link',
                    'Online'
                ) : Yii::t(
                    'app/wordpress-link',
                    'Offline'
                )
                ),
            ]
        ),
    ]) ?>
    <div class="container">
        <?php if ($isOnline): ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => $tableClassName],
                'rowOptions' => static function (WPFormInterface $model) {
                    $options = [
                        'onclick' => sprintf(
                            "window.location = '%s';",
                            Url::toRoute(['wordpress-link/fields', 'form_id' => $model['id']])
                        )
                    ];
                    if ($model->getIsActive()) {
                        $options['class'] = 'success';
                    }
                    return $options;
                },
                'columns' => [
                    'id',
                    [
                        'attribute' => 'name',
                        'format' => 'html',
                        'value' => static function (WPFormInterface $model) {
                            return sprintf(
                                '<a href="%s">%s</a>',
                                Url::toRoute(['wordpress-link/fields', 'form_id' => $model['id']]),
                                $model->getName()
                            );
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => static function (WPFormInterface $model) {
                            return sprintf(
                                '%s, %s',
                                $model->getStatusName(),
                                $model->getIsActive() ? Yii::t(
                                    'app/wordpress-link',
                                    'Active'
                                ) : Yii::t(
                                    'app/wordpress-link',
                                    'Inactive'
                                )
                            );
                        }
                    ],
                ],
            ]) ?>
        <?php endif ?>
    </div>
</div>