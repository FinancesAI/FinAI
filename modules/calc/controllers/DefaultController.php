<?php

namespace app\modules\calc\controllers;

use yii\web\Controller;

/**
 * Default controller for the `pact` module
 */
class DefaultController extends Controller
{

    public $layout = '@app/views/layouts/bank';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
