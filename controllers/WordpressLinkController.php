<?php

namespace app\controllers;

use app\base\Controller;
use app\components\WordpressLink;
use app\models\Setting;
use app\models\wordpress_link\FormsSearch;
use app\models\wordpress_link\FormSubmissionsSearch;
use app\models\wordpress_link\WsfForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * WordpressLink controller
 */
class WordpressLinkController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'submissions'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return Yii::$app->getUser()->getIdentity()->isAdmin();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * @return Setting
     * @throws InvalidConfigException if the setting component is not found
     */
    protected function getSettings(): Setting
    {
        return Yii::$app->get('setting');
    }

    /**
     * @return WordpressLink
     * @throws InvalidConfigException if the WordPress link component is not found
     */
    protected function getWordpressLink(): WordpressLink
    {
        return Yii::$app->get('wordpressLink');
    }

    /**
     * @param string $heading
     * @return void
     */
    protected function addBreadcrumbs(string $heading = ''): void
    {
        $name = Yii::t('app/wordpress-link', 'Wordpress Link');
        if (empty($heading)) {
            Yii::$app->view->title = $name;
            $this->view->params['breadcrumbs'][] = Yii::$app->view->title;
        } else {
            Yii::$app->view->title = sprintf(
                '%s | %s',
                $heading,
                $name
            );
            $this->view->params['breadcrumbs'][] = ['label' => $name, 'url' => ['index']];
            $this->view->params['breadcrumbs'][] = $heading;
        }
    }

    /**
     * Lists all remote WP forms.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex(): string
    {
       
        $searchModel = new FormsSearch();
        $this->addBreadcrumbs();

        return $this->render('index', [
            'isOnline' => $this->getWordpressLink()->getIsOnline(),
            'tableClassName' => $this->getSettings()->get('table_class'),
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    /**
     * @param int $id
     * @return WsfForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): WsfForm
    {
        if (($model = WsfForm::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app/wordpress-link', 'The requested page does not exist.'));
    }

    /**
     * Lists all remote WP form fields.
     *
     * @param int $form_id
     * @return string|Response
     * @throws InvalidConfigException if the database component is not found
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @todo Create model for form fields stored in DB
     * @todo Display how form fields relate to either Loan or Person model fields
     * @todo Request fields from remote DB to display them
     */
    public function actionFields(int $form_id)
    {
        $form = $this->findModel($form_id);
//        $searchModel = new FormFieldsSearch();
//        $searchModel->form_id = $form_id;
        $this->addBreadcrumbs(
            Yii::t(
                'app/wordpress-link',
                'Fields "{formName}"',
                [
                    'formName' => $form->getName(),
                ]
            )
        );

        return $this->render('fields', [
            'tableClassName' => $this->getSettings()->get('table_class'),
            'formId' => $form->getId(),
            'formName' => $form->getName(),
            'isActive' => $form->getIsActive(),
//            'searchModel' => $searchModel,
//            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    /**
     * Lists all remote WP form submissions.
     *
     * @param int $form_id
     * @return string|Response
     * @throws InvalidConfigException if the database component is not found
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSubmissions(int $form_id)
    {
        $form = $this->findModel($form_id);
        $searchModel = new FormSubmissionsSearch();
        $searchModel->form_id = $form_id;
        $this->addBreadcrumbs(
            Yii::t(
                'app/wordpress-link',
                'Submissions "{formName}"',
                [
                    'formName' => $form->getName(),
                ]
            )
        );

        return $this->render('submissions', [
            'tableClassName' => $this->getSettings()->get('table_class'),
            'formId' => $form->getId(),
            'formName' => $form->getName(),
            'isActive' => $form->getIsActive(),
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }


}
