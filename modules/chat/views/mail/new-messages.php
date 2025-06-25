<?php

/** @var ChatMessage[] $messages */
/** @var User $user */
/** @var View $this */

use app\models\User;
use app\modules\chat\models\ChatMessage;
use yii\web\View;

?>

<h3><?= Yii::t('modules/chat', 'You have new messages') ?>:</h3>

<?php
foreach ($messages as $message): ?>
    <i>
        <strong><?= $message->sender->fullname ?></strong>
        (<?= Yii::$app->formatter->asDatetime($message->created_at) ?>)
    </i>
    <br>
    <i><?= $message->text ?></i><br><br>
<?php
endforeach; ?>
