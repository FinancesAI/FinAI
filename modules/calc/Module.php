<?php

namespace app\modules\calc;

use Yii;

/**
 * pact module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\calc\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();
    }

    /**
     * @return void
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/calc'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/calc/messages',
            'sourceLanguage' => 'en',
            'forceTranslation' => true,
            'fileMap' => [
                'modules/calc' => 'calc.php',
            ],
        ];
    }
}
