<?php
namespace app\components\api;

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use yii\web\AssetBundle;
use yii\web\UploadedFile;
use app\models\Changes;
use app\models\Loan;

class InbankApi extends ApiModel {

    public function __construct($model) {
        $this->setModel($model);
        $this->setTitle("Inbank api");
        $this->setContent($this->_getContent());
        $this->setActions();
        InbankAsset::register(\Yii::$app->view);
    }

    private function _getContent() {
        $html = "<h2>".$this->getLabel()."</h2>";

//		if ($this->getModel()->person->credit_history == 3) {
//			if ($this->getModel()->extra) {
//				if ($this->getModel()->extra->inbank_contract_id) {
//					$html .= "<p>Inbank contract nr: ".$this->getModel()->extra->inbank_contract_id."</p>";
//					$html .= $this->getForm();
//				} else {
//					$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Send to inbank'), ['class' => 'btn btn-primary btn-block', 'id' => 'inbank-post-new']), ["class" => "form-group"]);
//					$html .= Html::tag("div", "", ["id" => "inbank-api-info", "class" => "hide"]);
//				}
//			} else {
//				$html .= "This loan don't have extra data";
//			}
//		} else {
//			if ($this->getModel()->extra && $this->getModel()->extra->inbank_contract_id) {
//				$html .= "Status isn't manual.";
//			} else {
//				$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Send to inbank'), ['class' => 'btn btn-primary btn-block', 'id' => 'inbank-post-new']), ["class" => "form-group"]);
//				$html .= Html::tag("div", "", ["id" => "inbank-api-info", "class" => "hide"]);
//			}
//		}

        return $html;
    }

    private function setActions() {
        if (\Yii::$app->getRequest()->post("inbankPost") == "true") {
            return $this->_postData();
        }

        if (\Yii::$app->getRequest()->post("inbankPostNew") == "true") {
            return $this->_postNew();
        }

        if (\Yii::$app->getRequest()->post("inbankStatus") == "true") {
            return $this->_postStatus();
        }
    }

    private function _postNew() {
        ob_end_clean();
        ob_start();
        // $api_url = 'https://staging-api.cofi.lv/applications';
        //$api_token = 'e46de7e1bcaaced9a54f1e9d0d2f800d';

        $months_values = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 21, 24, 27, 30, 33, 36, 39, 42, 45, 48, 51, 54, 57, 60, 66, 72);
        $api_term = $this->closest($months_values, $this->getModel()->term);
        if($this->getModel()->first_payment > 0) {
            $api_amount_total = $this->getModel()->amount - $this->getModel()->first_payment;
        } else {
            $api_amount_total = $this->getModel()->amount;
        }
        if ($api_amount_total >= 10000) {
            $api_amount = '10000';
        } else {
            $api_amount = $api_amount_total;
        }

        $apidata = array(
            'first_name'              => $this->getModel()->person->name,
            'last_name'               => $this->getModel()->person->surname,
            'personal_code'           => $this->getModel()->person->personal_code,
            'mobile'                  => $this->getModel()->person->phone,
            'asset_price'             => $api_amount,
            'payments'                => $api_term,
            'email'                   => $this->getModel()->person->email,
            'monthly_income'          => $this->getModel()->person->income,
            'gender'                  => $this->getModel()->person->gender,
            'doc_no'                  => 'A223456',
            'doc_type'                => 'id_card',
            'doc_valid_to'            => '31.12.2020',
            'employer'                => 'Affiliate Partners',
            'zip_code'                => 'LV1010',
            'monthly_liabilities'     => 0,
            'children'                => 0,
            'make'                    => 'Audi',
            'model'                   => 'A4',
            'bank_account'            => 'LV20HABA0551039118205',
            'first_registration_from' => 2000,

        );
        // auto id = 45
        // apvienosana id = 44
        // paterina id = 43
        // nekustamais ipasums id = 47

        if ($this->getModel()->product == 1) {
            $apidata['product_code'] = 'inbank_small_loan_standart_1aizdevums';
            $apidata['loan_purpose'] = 'other';
        } else if($this->getModel()->product == 3) {
            $apidata['product_code'] = 'inbank_small_loan_high_1aizdevums';
            $apidata['loan_purpose'] = 'refinance_existing_loans';
        } else {
            if($this->getModel()->amount <= '6000') {
                $apidata['product_code'] = 'CLP-car_loan_customer4';
            } else {
                $apidata['product_code'] = 'CLP-car_loan_customer_campaign5';
            }
        }

        $ch = curl_init(\Yii::$app->params["inbankapi_url"]."/applications");
        //$ch = curl_init("https://staging-api.cofi.lv/applications");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $apidata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: '.\Yii::$app->params["inbankapi_key"],
            //'Authorization: e46de7e1bcaaced9a54f1e9d0d2f800d',
            'Accept-Version:3',
        ]);

        $result = curl_exec($ch);

        $this->_log("REQUEST NEW - REQUEST - ".$result);

        //print_r($result);
        if (isset($result->error) || empty($result)) {
            if(isset($result->error)) {
                $errordsc = $result->error;
            } else {
                $errordsc = "Can't connect to host";
            }
        } else {
            $responseJson = json_decode($result);
            $errordsc = $responseJson->decision->message;
            $this->getModel()->extra->inbank_contract_id = $responseJson->application_number;
            $this->getModel()->extra->api_status_description = $responseJson->decision->message;
            $this->getModel()->extra->save();
        }

        echo $errordsc;
        \Yii::$app->end();
    }

    private function _postData() {
        ob_end_clean();
        ob_start();

        $files = UploadedFile::getInstanceByName("inbank_file");

        if ($files) {
            $ch = curl_init(\Yii::$app->params["inbankapi_url"]."/attachments");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                "application_number" => $this->getModel()->extra->inbank_contract_id,
                "attachments[]" => new \CURLFile($files->tempName)
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: '.\Yii::$app->params["inbankapi_key"],
                    'Accept-Version:3',
                ]
            );

            $result = curl_exec($ch);
            $content = "";

            if ($result === false){
                $this->_log("REQUEST ADD FILE ERROR - ".curl_error($ch));
                $content = "Error:" . curl_error($ch);
            } else {
                $this->_log("REQUEST ADD FILE OK - REQUEST - ".\Yii::$app->params["inbankapi_url"]."/attachments - RESULT - ".$result);
                $responseJson = json_decode($result);

                if ($responseJson) {
                    if ((property_exists($responseJson, "success"))) {
                        $content = "File uploaded succesfuly.";

                        if (in_array(98, $this->getModel()->actions)) {
                            Changes::setChanges(Loan::TYPE, $this->getModel()->id, "actions_98", null, 98);
                        } else  {
                            $this->getModel()->setAttribute("actions", $this->getModel()->actions + [98 => 1]);
                            $this->getModel()->save();
                        }

                    }
                    if ((property_exists($responseJson, "error"))) {
                        $content = "File uploaded error: ".$responseJson->error;
                    }
                }

            }
            curl_close($ch);

        } else {
            $content = "No file attached";
        }

        echo $content;
        \Yii::$app->end();
    }

    private function _postStatus() {
        ob_end_clean();
        ob_start();

        $ch = curl_init(\Yii::$app->params["inbankapi_url"]."/applications/statuses");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, ["application_numbers" => $this->getModel()->extra->inbank_contract_id]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: '.\Yii::$app->params["inbankapi_key"],
                'Accept-Version:3',
            ]
        );

        $result = curl_exec($ch);

        if ($result === false){
            $this->_log("REQUEST STATUS ERROR - ".curl_error($ch));
            $content = "Error:" . curl_error($ch);
        } else {
            $this->_log("REQUEST STATUS OK - REQUEST - ".\Yii::$app->params["inbankapi_url"]."/applications/statuses - RESULT - ".$result);
            $responseJson = json_decode($result);
            $content = "";

            if ($responseJson) {
                if ($responseJson->statuses) {
                    if ((property_exists($responseJson->statuses[0], "decision"))) {
                        $content = $responseJson->statuses[0]->decision->message;
                    } else {
                        $content = "Not updated yet or cancelled or approved.";
                    }
                } else {
                    $content = "No status.";
                }
            }
        }
        curl_close($ch);

        echo "<p>".$content."</p>";

        \Yii::$app->end();
    }

    private function getForm() {
        $html = "";
        $html .= "<p class=\"lead\">Get statuses</p>";
        $html .= Html::beginForm("", "", ["id" => "inbank-status-form"]);
        $html .= Html::input("hidden", "inbankStatus", "true", ["class" => "form-control"]);
        $html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Get status'), ['class' => 'btn btn-primary btn-block', 'id' => 'inbank-status-post']), ["class" => "form-group"]);
        $html .= Html::endForm();

        $html .= "<p class=\"lead\">Add new income proof</p>";
        $html .= Html::beginForm("", "", ["id" => "inbank-form", "enctype" => "multipart/form-data"]);
        $html .= Html::input("hidden", "inbankPost", "true", ["class" => "form-control"]);
        $html .= Html::tag("div", Html::input("file", "inbank_file", "", ["class" => "", "id" => "inbank-file"]), ["class" => "form-group"]);
        $html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Post data'), ['class' => 'btn btn-primary btn-block', 'id' => 'inbank-post']), ["class" => "form-group"]);
        $html .= Html::endForm();

        $html .= Html::tag("div", "", ["id" => "inbank-api-info", "class" => "hide"]);

        return $html;
    }

    private function _log($msg) {
        $fd = fopen(\Yii::$app->params["inbankapi_log_path"], "a+");
        $str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
        fwrite($fd, $str . "\n");
        fclose($fd);
    }

    private function closest($array, $number)
    {

        sort($array);
        foreach ($array as $a) {
            if ($a >= $number) return $a;
        }

        return end($array); // or return NULL;
    }
}

class InbankAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];

    public $js = [
        'js/inbank.js?v=4',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}