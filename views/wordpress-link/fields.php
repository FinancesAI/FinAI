<?php
/**
 * @see app\controllers\WordpressLinkController::actionFields()
 * @author Nils <deele@tuta.io>
 */

/**
 * @var yii\web\View $this
 * @var string $tableClassName
 * @var int $formId
 * @var string $formName
 * @var bool $isActive
 */
?>
<div class="wordpress-link wordpress-link_fields">
    <div class="h1"><?= Yii::t(
            'app/wordpress-link',
            'WordPress Link'
        ) ?></div>
    <?= $this->render("/admin/_nav") ?>
    <div class="container">
        <?= $this->render('_header', [
            'heading' => Yii::t(
                'app/wordpress-link',
                'Fields'
            ),
            'formId' => $formId,
            'formName' => $formName,
            'isActive' => $isActive,
        ]) ?>

    </div>
</div>
