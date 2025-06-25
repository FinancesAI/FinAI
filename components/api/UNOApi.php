<?php
namespace app\components\api;

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use yii\web\AssetBundle;

class UNOApi extends ApiModel {

	public function __construct($model) {
		$this->setModel($model);
		$this->setTitle("UNO api");
		$this->setContent($this->_getContent());
		$this->setActions();
		UNOAsset::register(\Yii::$app->view);
	}
	
	private function setActions() {
		if (\Yii::$app->getRequest()->post("unoPost") == "true") {
			return $this->_postData();
		}
		if (\Yii::$app->getRequest()->post("unoStatus") == "true") {
			return $this->_postStatus();
		}
		if (\Yii::$app->getRequest()->post("unoCancel") == "true") {
			return $this->_postCancel();
		}
		if (\Yii::$app->getRequest()->post("unoSigned") == "true") {
			return $this->_postSigned();
		}
	}
	
	private function _getContent() {
		$html = "<h2>".$this->getLabel()."</h2>";

		$html .= $this->getForm();
		
		return $html;
	}
	
	private function getForm() {
		$jsonForm = require_once \Yii::$aliases["@app"]."/components/api/forms/formData.php";
		if (!$jsonForm) {
			return "Cant find form";
		}
		$html = "";
		
		if ($this->_getContractId()) {
			$html .= "<p>Data already sent, contract id: ".$this->_getContractId()."</p>";
		} else {
			$html  .= Html::beginForm("", "", ["id" => "uno-form"]);
			$html  .= Html::input("hidden", "unoPost", "true", ["class" => "form-control"]);
			
			foreach (json_decode($jsonForm) as $form) {
				$value = $this->getValue($form->input_id);
						
				if ($form->input_required) {
					$req = ($value ? ["class" => "form-control hide", "required" => "required"] : ["class" => "form-control", "required" => "required"]);
				} else {
					$req = ($value ? ["class" => "form-control hide"] : ["class" => "form-control"]);
				}
				
				if ($form->input_id == 110) {
					$req = array_merge_recursive($req, ["placeholder" => "dd.mm.yyyy"]);
				}
				
				if ($form->input_id == 139) {
					$req = ["class" => "form-control", "required" => "required"];
				}
				
				if (in_array($form->input_id, ["160", "107", "141", "138", "123", "122", "140", "115", "116", "113"])) {
					$req = ["class" => "hide"];
				} else {
					if (!$value || $form->input_id == 139)
						$html .= Html::label($form->input_name);
						
				}
				//debug
				//$html .= Html::label($form->input_name." ".$form->input_id);
				//$req = ($value ? ["class" => "form-control"] : ["class" => "form-control"]);
				
				if ($form->input_type == "select") {
					$vals = [];
					foreach ($form->input_value as $valId => $valVal) {
						$vals[$valVal] = $valVal;
					}
					
					$html .= Html::tag("div", Html::dropDownList($form->input_id, $value, $vals, $req), ["class" => "form-group"]);
				} elseif ($form->input_type == "text") {
					$html .= Html::tag("div", Html::input("text", $form->input_id, $value, $req), ["class" => "form-group"]);
				} elseif ($form->input_type == "checkbox") {
					$html .= Html::tag("div", Html::checkbox($form->input_id, $value, $req), ["class" => "form-group"]);
				}
			}
			$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Post data'), ['class' => 'btn btn-primary btn-block', 'id' => 'uno-post']), ["class" => "form-group"]);
			$html .= Html::endForm();
		}

		$html .= Html::tag("div", "", ["id" => "uno-api-info", "class" => "hide"]);
		
		return $html;
	}
	
	private function getValue($formNr) {
		$value = null;
		if ($formNr == 100) {
			$value = $this->getModel()->person->name;
		} elseif ($formNr == 101) {
			$value = $this->getModel()->person->surname;
		} elseif ($formNr == 102) {
			$value = $this->getModel()->person->personal_code;
		} elseif ($formNr == 104) {
			$value = $this->getModel()->person->phone;
		} elseif ($formNr == 107) { //apgādājamie
			$value = 0;
		} elseif ($formNr == 123) { //pilseta/ciemats/pagasts
			$value = "-";
		} elseif ($formNr == 126) {
			$value = $this->getModel()->person->email;
		} elseif ($formNr == 128) {
			$value = $this->getModel()->person->phone;
		} elseif ($formNr == 107) {
			$value = 0;
		} elseif ($formNr == 111) {
			$value = $this->getModel()->person->income;
		} elseif ($formNr == 137) {
			$value = "Nē";
		} elseif ($formNr == 138) {
			$value = "Nē";
		} elseif ($formNr == 130) {
			$value = $this->getModel()->first_payment;
		} elseif ($formNr == 132) {
			$value = $this->getModel()->term;
		} elseif ($formNr == 131) {
			$value = $this->getModel()->amount;
		} elseif ($formNr == 113) {
			$value = 0;
		} elseif ($formNr == 117) {
			$value = "Bigbank";
		} elseif ($formNr == 139) {
			$value = $this->getModel()->getProducts()[$this->model->product];
		} elseif ($formNr == 118) {
			$value = "Darbinieks";
		} elseif ($formNr == 119) {
			$value = "AA1234567";
		} elseif ($formNr == 120) {
			$value = "Ropažu iela";
		} elseif ($formNr == 121) {
			$value = "10";
		} elseif ($formNr == 124) {
			$value = "Rīga";
		} elseif ($formNr == 125) {
			$value = "LV-1039";
		} elseif ($formNr == 136) {
			$value = "Pieteikums nav parakstīts";
		} elseif ($formNr == 160) {
			$value = "LV08HABA0551023395885";
		}

		return $value;
	}
	
	private function _postData() {
		ob_end_clean();
		ob_start();

		$fields = [];
		$fields["key"] = \Yii::$app->params["unoapi_key"];
		//$fields["carhp"] = 1;
		foreach (\Yii::$app->getRequest()->post() as $key => $value) {
			if (is_numeric($key))
				$fields["input_".$key] = ["input_value" => $value];
		}
		
		//open connection
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, \Yii::$app->params["unoapi_url"]."?post=data&carhp=1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		//execute
		$result = curl_exec($ch);
		if ($result === false){
			$this->_log("REQUEST ERROR - ".curl_error($ch));
				
			echo "Error:" . curl_error($ch);
		} else {
			$this->_log("REQUEST OK - REQUEST - ".http_build_query($fields)." - RESULT - ".$result);
				
			echo $result;
			$resJson = json_decode($result);
			if ($resJson->error == false)
				$this->_setContractId($resJson->contract_id);
		}
		curl_close($ch);
		
		$content = ob_get_contents();
		ob_end_clean();
		
		echo $content;
		\Yii::$app->end();
	}

	private function _setContractId($id) {
		$this->_log("SET_CONTRACT_ID - CONTRACT_ID - ".$id." - MODEL_ID - ".$this->getModel()->id);
		
		\Yii::$app->db->createCommand("INSERT INTO `uno_api` (`id`, `loan_id`, `contract_id`) VALUES (NULL, {$this->getModel()->id}, {$id});")->execute();
	}
	
	private function _getContractId() {
		if ($data = \Yii::$app->db->createCommand("SELECT *  FROM `uno_api` WHERE `loan_id` = ".$this->getModel()->id)->queryOne()) {
			return $data["contract_id"];
		}
		
		return null;
	}
	
	private function _log($msg) {
		$fd = fopen(\Yii::$app->params["unoapi_log_path"], "a+");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $str . "\n");
		fclose($fd);
	}
}

class UNOAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $css = [
	];

	public $js = [
			'js/uno.js?v=2',
	];

	public $depends = [
			'yii\web\YiiAsset',
			'yii\bootstrap\BootstrapAsset',
	];
}