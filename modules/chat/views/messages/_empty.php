<?php

use yii\helpers\Html;

?>
<div class="row align-self-center h-100 ng-hide"
     ng-show="initialStateLoaded === true && !hasContacts() && !conversationsQuery.length">
    <div class="no-contacts align-self-center m-auto p-5">
        <div class="text-center">
            <h4 class="text-gray-dark"><?= Yii::t('modules/chat', 'No contacts') ?></h4>
            <p class="text-gray">
                <?= Yii::t('modules/chat', 'You don\'t have any conversations yet') ?>
            </p>
        </div>
    </div>
</div>
