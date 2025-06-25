<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\helpers\Icon;

/* @var $this yii\web\View */
?>
<div class="conversation-input pl-5 pt-3 pb-3 pr-5" ng-show="currentContact != null" ng-cloak>
    <script type="text/ng-template" id="'templates/popover/popover-template.html'">
        <div class="popover bs-popover-top" role="tooltip">
            <div class="arrow"></div>
            <h3 class="popover-header" ng-bind="uibTitle" ng-if="uibTitle"></h3>
            <div class="popover-body"
                 uib-tooltip-template-transclude="contentExp()"
                 tooltip-template-transclude-scope="originScope()">
            </div>
        </div>
    </script>
    <div class="input-group">
        <input ng-model="message" type="text" class="form-control message-input" my-enter="sendMessage()"
               placeholder="<?= Yii::t('modules/chat', 'Enter your message...') ?>">
        <div class="input-group-append">
            <button type="button" class="btn btn-secondary" ng-click="sendMessage()" ng-disabled="!message">
                <?= Icon::fe('send') ?>
            </button>
        </div>
    </div>
</div>
