<?php

namespace app\modules\chat;

use Yii;

/**
 * chat module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'app\modules\chat\controllers';

    /**
     * @var string
     */
    public $layout = 'main';

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();

        $this->components = [
            'chatMessageManager' => [
                'class' => 'app\modules\chat\components\ChatMessageManager',
            ],
            'userManager' => [
                'class' => 'app\modules\chat\components\UserManager',
            ],
            'chatMailer' => [
                'class' => 'app\modules\chat\components\ChatMailer',
            ],
        ];
    }

    /**
     * @return void
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/chat'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/chat/messages',
            'sourceLanguage' => 'en',
            'forceTranslation' => true,
            'fileMap' => [
                'modules/chat' => 'chat.php',
            ],
        ];
    }
}
