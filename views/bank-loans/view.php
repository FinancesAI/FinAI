<?php
use app\models\Loan;
use app\models\LoanProgerss;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */
/* @var $thisUserLoanProgress array */

$this->title                   = Yii::t( "app/loan", "Update Loan Data" );
$this->params['breadcrumbs'][] = [ 'label' => Yii::t( 'app/loan', 'Loans' ), 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-view">

    <h1><?= Html::encode( $this->title ) ?></h1>

    <div class="row">

     <div class="col-xs-12 col-sm-12 col-md-4">
            <?php if ($model->loan_type == 1): ?>
                    <h2><?= Yii::t("app/loan", "LEGAL PERSON CREDIT DATA") ?></h2>
                <?php else: ?>
                    <h2><?= Yii::t("app/loan", "CREDIT DATA") ?></h2>
                <?php endif; ?>
    <?php
    $documentAttribute = null;
    if (!empty($model->document)) {
        $documentAttribute = [
            'attribute' => 'document',
            'label'     => Yii::t( 'app/loan', 'Document' ),
            'value'     => '<a href="https://www.finlat.lv/document/' . $model->document . '" target="_blank">' . Yii::t( 'app/loan', 'Document' ) . '</a>',
            'format'    => 'raw',
            'options'   => ['target' => '_blank']
        ];
    }

$carAdLinkAttribute = null;
if (!empty($model->car_ad_link)) {
    $shortenedLink = substr($model->car_ad_link, 0, 10) . '...'; // Shorten the link
    $carAdLinkAttribute = [
        'attribute' => 'car_ad_link',
        'label'     => Yii::t('app/loan', 'Link'),
        'value'     => Html::a($shortenedLink, $model->car_ad_link, ['target' => '_blank']), 
        'format'    => 'raw',
        'options'   => ['target' => '_blank']
    ];
}



 
    $attributes = [
        'id', 
        [
        "attribute" => "create_time",
        "value"     => Yii::$app->formatter->asDatetime($model->create_time, 'php:Y-m-d H:i:s') ?: '-',
         ],
        [
            "attribute" => "source",
            'label'     => Yii::t( 'app/loan', 'source' ),
            "value"     => $model->getValue( "source", $model ) ?: '-',
        ],
        [
            "attribute" => "product",
            "value"     => $model->getValue( "product", $model ) ?: '-',
        ],
        [
            "attribute" => "amount",
            "value"     => $model->getValue( "amount", $model ) ?: '-',
        ],
        [
            "attribute" => "term",
            "value"     => $model->getValue( "term", $model ) ?: '-',
        ],
        [
        "attribute" => "realEstate",
        "label" => Yii::t('app/loan', 'Real Estate'),
        "value" => function($model) {
            $value = $model->getAttribute('realEstate');
            if ($value == 'J') {
                return Yii::t('app/loan', 'YES');
            } else {
                return Yii::t('app/loan', 'NO');
            }
        },
        "format" => "raw",
    ], 
        'property_type',
        // 'property_address',
        'car_brand',
        'car_model',
        'company_name',
        'autoregnr',
        // 'amount_taken',
        'revenue',
        'company_length_of_service',
        'loan_purpose',
        'owned_transport',
        'total_credit_balance',
         'year_of_issue',
        'additional_credit_amount',
        'company_turnover',
        'company_duration_months',
        'invoice_amount',
        'step_monthly_payment',
       
  

       
      
    ];
    if ($model->invoice_due_date !== null) {
    $attributes[] = [
        "attribute" => "invoice_due_date",
        "label" => Yii::t('app/loan', 'DESIRED FACTORING TERM'),
        "value" => $model->invoice_due_date,
        "format" => "raw",
    ];
}



    if ($carAdLinkAttribute) {
        $attributes[] = $carAdLinkAttribute;
    }

    if ($documentAttribute) {
        $attributes[] = $documentAttribute;
    }

    $attributes = array_filter($attributes, function ($attribute) use ($model) {
        if (is_array($attribute)) {
            $value = isset($attribute['value']) ? $attribute['value'] : null;
            return !empty($value);
        } else {
            return !empty($model->$attribute);
        }
    });

    echo DetailView::widget([
        'model'      => $model,
        'attributes' => $attributes,
    ] );
    ?>
      <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
    <?= $form->field($model, 'comment')->textarea([
    'rows' => 4

    ])->label(Yii::t("app/loan", "COMMENT")) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

  

<div class="col-xs-12 col-sm-12 col-md-4">
     <?php if ($model->loan_type == 1): ?>
       <h2><?= Yii::t("app/person", "LEGAL PERSON DATA") ?></h2>
       <?php else: ?>
        <h2><?= Yii::t("app/person", "PERSON DATA") ?></h2>
      <?php endif; ?>    

    <?php
    $personAttributes = [
        'id',
        'name',
        'surname',
        'company_name',
        'registration_number',
        'company_duration_months',
        'company_turnover',
        'city',
        'phone',
        'email',
        'personal_code',
        'outcome',
        // 'address',
        'marital_status',
        'dependants',
        'workplace',
        'position',
        'length_of_service',
        'income',
    ];

   
    $personAttributes = array_filter($personAttributes, function ($attribute) use ($model) {
        return !empty($model->person->$attribute);
    });

    echo DetailView::widget( [
        'model'      => $model->person,
        'attributes' => $personAttributes,
    ] );
    ?>
</div>


        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="loan-form">
                <h2><?= Yii::t( 'app/loan', 'Update Status' ) ?></h2>
                <?php $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col-xs-12">
                        <label class="control-label" for="prog-status"><?= Yii::t( 'app/loan', 'Status')?></label>
                        <?= Html::dropDownList( "prog-status", $thisUserLoanProgress['status'], LoanProgerss::getStatuses(),
                            [ "class" => "form-control", 'id' => 'prog-status' ] ) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <label for="prog-amount"><?= Yii::t( 'app/loan', 'Approved amount' ) ?></label>
                        <input type="text" id="prog-amount" name="prog-val"
                               value="<?= $thisUserLoanProgress['amount'] ?>"
                               class="form-control prog-amount">
                        <label for="prog-comment"><?= Yii::t( 'app/loan', 'Comment' ) ?></label>
                        <textarea id="prog-comment" name="prog-comment"
                                  class="form-control prog-comment"><?= $thisUserLoanProgress['text'] ?></textarea>
                    </div>
                </div>

                <p></p>

                <div class="row">
                    <div class="col-xs-12">

                        <div class="form-group">
                            <?= Html::submitButton( Yii::t( 'app/loan', 'Save' ), [ 'class' => 'btn btn-primary btn-block' ] ) ?>
                        </div>
                    </div>
                </div>

                <?php if (Yii::$app->user->identity->role == 2 && Yii::$app->user->identity->chat_enable == 1): ?>

                <div class="row">
                    <div class="col-xs-12">

                        <div class="form-group">
                            <?= Html::a(Yii::t('app/site', 'Chat'), [Url::toRoute( [ "/chat/messages" ] )], ['class' => 'btn btn-primary btn-block']) ?>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div> 
