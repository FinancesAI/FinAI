<?php
/**
 * @author Nils <deele@tuta.io>
 */

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $heading
 * @var int $formId
 * @var string $formName
 * @var bool $isActive
 */

?>
<div class="page-heading" style="display: flex; align-items: baseline; column-gap: 1rem;">
    <h1><?= $heading ?></h1>
    <h2><?= $formName ?></h2>
    <span class="badge badge-pill badge-<?= ($isActive ? 'success' : 'warning') ?>>"><?= ($isActive ? Yii::t(
            'app/wordpress-link',
            'Active'
        ) : Yii::t(
            'app/wordpress-link',
            'Inactive'
        )
        ) ?></span>
    <?= Html::a(
        Yii::t(
            'app/wordpress-link',
            'Fields'
        ),
        ['wordpress-link/fields', 'form_id' => $formId],
        ['class' => 'btn btn-info']
    ) ?>
    <?= Html::a(
        Yii::t(
            'app/wordpress-link',
            'Submissions'
        ),
        ['wordpress-link/submissions', 'form_id' => $formId],
        ['class' => 'btn btn-info']
    ) ?>
</div>
