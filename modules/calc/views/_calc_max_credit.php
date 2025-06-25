<?php

use app\modules\calc\assets\CalcMaxCreditAsset;

$bundle = CalcMaxCreditAsset::register($this);
?>

<div class="cmc-page">
    <div class="cmc-page__container">
        <img class="cmc-page__logo" src="<?= $bundle->baseUrl ?>/images/logo.png">
        <div class="cmc-page__title">FĪNANŠU SALĪDZINĀSANAS<br>PLATFORMA</div>
        <div class="cmc-page__calc-title-bg"></div>
        <div class="cmc-page__calc-title">Kādu summu tu vari aizņemties?</div>
        <div id="calc-max-credit" class="cmc">
            <div class="cmc-container">
                <form class="cmc-form" name="cmc">
                    <div class="cmc-field">
                        <div class="cmc-field__label">Aizdevums paredzēts</div>
                        <div class="cmc-field__inputs">
                            <div class="cmc-field__inputs cmc-radiobox">
                                <label class="cmc-radio">
                                    <input class="cmc-radio__input js-cmc-calculate-change" type="radio" name="borrowers" value="1" checked>
                                    <span class="cmc-radio__mark"></span>
                                    Tikai man
                                </label>
                                <label class="cmc-radio">
                                    <input class="cmc-radio__input js-cmc-calculate-change" type="radio" name="borrowers" value="2">
                                    <span class="cmc-radio__mark"></span>
                                    Man un līdzaizņēmējam
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="cmc-field">
                        <div class="cmc-field__label">Procentu likme<span class="cmc-helper" title=""></span></div>
                        <div class="cmc-field__inputs">
                            <input class="cmc-input js-cmc-calculate-input" type="text" inputmode="numeric" name="rate" value="2.6">
                            <div class="cmc-field__unit">%</div>
                        </div>
                    </div>
                    <div class="cmc-field">
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="1">Ikmēneša ienākumi pēc nodokļu nomaksas<span class="cmc-helper" title="Mēneša alga vai citi ienākumi, tostarp pensija, sociālais pabalsts, dotācijas, īpašuma ienākumi. Ja piesakies ar līdzaizņēmēju, kredīta kalkulatorā norādi kopējos ienākumus."></span></div>
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="2" style="display:none">Ģimenes ikmēneša ienākumi pēc nodokļu nomaksas<span class="cmc-helper" title="Mēneša alga vai citi ienākumi, tostarp pensija, sociālais pabalsts, dotācijas, īpašuma ienākumi. Ja piesakies ar līdzaizņēmēju, kredīta kalkulatorā norādi kopējos ienākumus."></span></div>
                        <div class="cmc-field__inputs">
                            <input class="cmc-input js-cmc-calculate-input" type="text" inputmode="numeric" name="monthlyIncome" value="1500">
                            <div class="cmc-field__unit">EUR</div>
                        </div>
                    </div>
                    <div class="cmc-field">
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="1">Ikmēneša maksājumi par esošajām kredītsaistībām<span class="cmc-helper" title="Ikmēneša maksājumi par kredītsaistībām, ieskaitot hipotekāro kredītu, līzingu, patēriņa kredītu u.c. kredītsaistībām."></span></div>
                        <div class="cmc-field__label js-cmc-for-borrowers" data-for-borrowers="2" style="display:none">Ģimenes ikmēneša maksājumi par esošajām kredītsaistībām<span class="cmc-helper" title="Ikmēneša maksājumi par kredītsaistībām, ieskaitot hipotekāro kredītu, līzingu, patēriņa kredītu u.c. kredītsaistībām."></span></div>
                        <div class="cmc-field__inputs">
                            <input class="cmc-input js-cmc-calculate-input" type="text" inputmode="numeric" name="monthLiabilities" value="0">
                            <div class="cmc-field__unit">EUR</div>
                        </div>
                    </div>
                </form>
                <div class="cmc-results">
                    <div class="cmc-mainresult">
                        <div class="cmc-mainresult__label">Tava maksimālā kredīta summa:</div>
                        <div class="cmc-mainresult__value"><span class="js-cmc-max-amount"></span>&nbsp;EUR</div>
                    </div>
                    <div class="cmc-result">
                        <div class="cmc-result__label">Tava maksimālā kredīta summa:</div>
                        <div class="cmc-result__value"><span class="js-cmc-max-monthly-payment"></span>&nbsp;EUR</div>
                    </div>
                </div>
                <div class="cmc-btn-wrap">
                    <a class="cmc-btn" href="#">Pieteikties</a>
                </div>
                <div class="cmc-bottom-desc"><?= Yii::t('modules/calc', 'bottom_desc') ?></div>
            </div>
        </div>
    </div>
</div>