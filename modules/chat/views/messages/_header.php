<?php

use app\helpers\Icon;

?>
<div class="conversation-header pl-5 pt-3 pb-3 pr-3" ng-show="currentContact != null" ng-cloak>
    <div class="row align-items-center justify-content-sm-start h-100">
        <div class="col-2 col-sm-2 col-md-2 col-lg-1">
            <a class="avatar float-left"
               href="{{ currentContact.url }}"
               ng-style="{'background-image': 'url(' + currentContact.avatar + ')'}">
                <span class="avatar-status bg-green" ng-show="currentContact.online"></span>
                <span class="avatar-status bg-gray" ng-show="!currentContact.online"></span>
            </a>
        </div>
        <div class="col-4 col-sm-6 col-md-6 col-lg-7">
            <div>
                <a href="{{ currentContact.url }} "><strong>{{ currentContact.fullName }}</strong></a>
                <div class="d-inline-block verified" ng-show="currentContact.admin"
                     rel="tooltip" title="<?= Yii::t('modules/chat', 'Admin user') ?>">
                    <?= Icon::fe('check') ?>
                </div>
            </div>
            <div><small>{{ currentContact.username }}</small></div>

            <div ng-show="!currentContact.online && currentContact.lastTimeOnline"><small><?= Yii::t('modules/chat', 'Online') ?> {{ currentContact.lastTimeOnline }}</small></div>
        </div>
    </div>
</div>
