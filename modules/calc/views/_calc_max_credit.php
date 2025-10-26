<?php

use app\modules\calc\assets\CalcMaxCreditAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

$bundle = CalcMaxCreditAsset::register($this);

$currencySign = Yii::$app->params['calc.currencySign'] ?? '₽';
$currencyCode = Yii::$app->params['calc.currencyCode'] ?? 'RUB';

$defaultCalcConfig = [
    'term' => 20,
    'calcMonthlyPayment' => 160,
    'fixedHouseholdCosts' => 560,
    'guarantorCosts' => 405,
    'householdMemberCosts' => 225,
    'pl_buffer' => [
        'pl_buffer_parameter' => 3,
        'pl_existing_coef' => 1.37,
        'pl_liabilities' => 20000,
        'pl_off' => 0,
    ],
    'dsti_calculation' => [
        'dsti_parameter' => 50,
        'dsti_interest' => 6,
        'dsti_coefficient' => 1.49,
        'dsti_liabilities' => 20000,
        'dsti_off' => 0,
    ],
    'periodForMaxLti' => 72,
    'pl_buffer_parameter' => 3,
    'max_month_credit_percentage' => 40,
];

$calcConfig = ArrayHelper::merge($defaultCalcConfig, Yii::$app->params['calc.maxCreditConfig'] ?? []);

$this->registerJs(
    'window.calcMaxCreditConfig = ' . Json::encode($calcConfig) . ';' .
    'window.calcMaxCreditCurrency = ' . Json::encode([
        'sign' => $currencySign,
        'code' => $currencyCode,
    ]) . ';',
    \yii\web\View::POS_HEAD
);
?>

<div class="cmc-page">
    <div class="cmc-page__container">
        <img class="cmc-page__logo" src="<?= $bundle->baseUrl ?>/images/logo.png">
        <div class="cmc-page__title"><?= Yii::t('modules/calc', 'platform_title') ?></div>
        <div class="cmc-page__calc-title-bg"></div>
        <div class="cmc-page__calc-title"><?= Yii::t('modules/calc', 'calc_title') ?></div>
        <div id="calc-max-credit" class="cmc">
            <div class="cmc-container">
                <form class="cmc-form" name="cmc">
                    <div class="cmc-field">
                        <div class="cmc-field__label"><?= Yii::t('modules/calc', 'loan_for_label') ?></div>
                        <div class="cmc-field__inputs">
                            <div class="cmc-field__inputs cmc-radiobox">
                                <label class="cmc-radio">
                                    <input class="cmc-radio__input js-cmc-calculate-change" type="radio" name="borrowers" value="1" checked>
                                    <span class="cmc-radio__mark"></span>
                                    <?= Yii::t('modules/calc', 'loan_for_single') ?>
                                </label>
                                <label class="cmc-radio">
                                    <input class="cmc-radio__input js-cmc-calculate-change" type="radio" name="borrowers" value="2">
                                    <span class="cmc-radio__mark"></span>
                                    <?= Yii::t('modules/calc', 'loan_for_couple') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="cmc-field">
                        <div class="cmc-field__label"><?= Yii::t('modules/calc', 'rate_label') ?><span class="cmc-helper" title=""></span></div>
                        <div class="cmc-field__inputs">
                            <input class="cmc-input js-cmc-calculate-input" type="text" inputmode="numeric" name="rate" value="2.6">
                            <div class="cmc-field__unit">%</div>
                        </div>
                    </div>
                    <div class="cmc-field">
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="1"><?= Yii::t('modules/calc', 'income_label_single') ?><span class="cmc-helper" title="<?= Yii::t('modules/calc', 'income_hint') ?>"></span></div>
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="2" style="display:none"><?= Yii::t('modules/calc', 'income_label_couple') ?><span class="cmc-helper" title="<?= Yii::t('modules/calc', 'income_hint') ?>"></span></div>
                        <div class="cmc-field__inputs">
                            <input class="cmc-input js-cmc-calculate-input" type="text" inputmode="numeric" name="monthlyIncome" value="1500">
                            <div class="cmc-field__unit"><?= $currencySign ?></div>
                        </div>
                    </div>
                    <div class="cmc-field">
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="1"><?= Yii::t('modules/calc', 'liabilities_label_single') ?><span class="cmc-helper" title="<?= Yii::t('modules/calc', 'liabilities_hint') ?>"></span></div>
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="2" style="display:none"><?= Yii::t('modules/calc', 'liabilities_label_couple') ?><span class="cmc-helper" title="<?= Yii::t('modules/calc', 'liabilities_hint') ?>"></span></div>
                        <div class="cmc-field__inputs">
                            <input class="cmc-input js-cmc-calculate-input" type="text" inputmode="numeric" name="monthLiabilities" value="0">
                            <div class="cmc-field__unit"><?= $currencySign ?></div>
                        </div>
                    </div>
                </form>
                <div class="cmc-results">
                    <div class="cmc-mainresult">
                        <div class="cmc-mainresult__label"><?= Yii::t('modules/calc', 'max_credit_label') ?></div>
                        <div class="cmc-mainresult__value"><span class="js-cmc-max-amount"></span>&nbsp;<?= $currencySign ?></div>
                    </div>
                    <div class="cmc-result">
                        <div class="cmc-result__label"><?= Yii::t('modules/calc', 'max_monthly_payment_label') ?></div>
                        <div class="cmc-result__value"><span class="js-cmc-max-monthly-payment"></span>&nbsp;<?= $currencySign ?></div>
                    </div>
                </div>
                <div class="cmc-btn-wrap">
                    <a class="cmc-btn" href="#"><?= Yii::t('modules/calc', 'apply_button') ?></a>
                </div>
                <div class="cmc-bottom-desc"><?= Yii::t('modules/calc', 'bottom_desc') ?></div>
            </div>
        </div>
    </div>
</div>
