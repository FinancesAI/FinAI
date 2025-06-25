<?php

use app\helpers\Icon;

?>
<div class="conversation-items d-flex">
    <div class="wrapper-items w-100" scroll-glue>
        <div class="items p-5">
            <div ng-cloak ng-repeat="item in messages track by item.id">
                <div class="date" ng-show="checkMessageDatetime(item.datetime)">
                    <span>{{ getCurrentDate() }}</span>
                </div>
                <div class="item {{ item.type }} {{ getItemClasses(item) }}"
                     ng-mouseover="onMessageHover(item)"
                     ng-click="toggleMessage(item.id)"
                     scroll-glue-anchor>
                    <div class="item-body d-flex flex-row align-items-center {{ item.type == 'sent' ? 'flex-row-reverse' : '' }}">
                        <span class="avatar" ng-style="{'background-image': 'url(' + item.user.avatar + ')'}"></span>
                        <div ng-show="item.text.length > 0" class="text {{ item.type == 'sent' ? 'sent-text text-gray-dark' : 'received-text text-gray-dark' }} p-2 rounded">
                            {{ item.text }}
                        </div>
                        <small class="time text-gray">{{ getTime(item.datetime) }}</small>
                        <div ng-show="item.type == 'sent'" class="cm-message-status__icon"><div title="" class="cm-check {{ item.is_new ? 'cm-check-sended' : 'cm-check-read' }}"></div></div>
                        <span class="spinner" ng-show="isMessagePending(item)">
                            <?= Icon::fa('spinner', ['class' => 'fa-spin']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
