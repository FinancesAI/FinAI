<?php
/**
 * Created by PhpStorm.
 * User: Marcis
 * Date: 01.05.2018.
 * Time: 23:32
 */

namespace app\components;

use app\models\Loan;
use yii\bootstrap\Html;

class SendBulkSMS {

	public function __construct() {
	}

	public static function renderModalBody( $data = '' ) { ?>

        <form method="post" action="<?= \Yii::$app->urlManager->createUrl( [ "sms/send2" ] ) ?>"
              enctype="multipart/form-data">
            <input type="hidden" name="_csrf" value="<?= \Yii::$app->getRequest()->csrfToken ?>">

	        <?= Html::checkboxList( 'statuses', '', Loan::getStatuses() ) ?>
	        <?= Html::hiddenInput( "send-sms", "true" ) ?>

            <div class="form-group">
                <label for="sms-box">Sms</label>
                <select class="form-control" name="type" id="sms-box">
                    <option value="2"><?= \Yii::t( "app/loan", "Information sent to email" ) ?></option>
                    <option value="3"><?= \Yii::t( "app/loan", "Send address" ) ?></option>
                    <option value="7"><?= \Yii::t( "app/loan", "Contact request sms" ) ?></option>
                    <option value="1"><?= \Yii::t( "app/loan", "Reject loan sms" ) ?></option>
                    <option value="9"><?= \Yii::t( "app/loan", "Accept, contact us sms" ) ?></option>
                    <option value="4"><?= \Yii::t( "app/loan", "Send custom sms" ) ?></option>
                </select>
            </div>

            <div id="sms-4" class="hide">
                <div class="form-group">
                    <label for="sms-custom-text">Custom text</label>
                    <textarea class="form-control" id="sms-custom-text" name="sms_custom_text">Labdien,</textarea>
                </div>
            </div>


            <button class="btn btn-block btn-primary" type="submit"
                    data-confirm="Are you sure?"><?= \Yii::t( "app/loan", "Send sms" ) ?></button>
        </form>


		<?php
	}


}