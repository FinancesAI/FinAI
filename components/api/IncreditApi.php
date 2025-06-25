<?php
namespace app\components\api;

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use yii\web\AssetBundle;
use yii\web\UploadedFile;
use app\models\Changes;
use app\models\Loan;

class IncreditApi extends ApiModel {

	public function __construct($model) {
		$this->setModel($model);
		$this->setTitle("Incredit api");
		$this->setContent($this->_getContent());
		$this->setActions();
		IncreditAsset::register(\Yii::$app->view);
	}
	
	private function _getContent() {
		$html = "<h2>".$this->getLabel()."</h2>";
		if ($this->_getContractId()) {
			$html .= "<p>Data already sent, contract id: ".$this->_getContractId()."</p>";
		} else {
			$html .= Html::beginForm("", "", ["id" => "incredit-form"]);
			$html .= Html::input("hidden", "increditPost", "true", ["class" => "form-control"]);
			
			$html .= Html::tag("p", "Person information", ["class" => "lead"]);
			$html .= Html::label("Name");
			$html .= Html::tag("div", Html::input("text", "peron_name", $this->getModel()->person->name, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Surname");
			$html .= Html::tag("div", Html::input("text", "person_surname", $this->getModel()->person->surname, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Personal code");
			$html .= Html::tag("div", Html::input("text", "person_pk", $this->getModel()->person->personal_code, ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Loan information", ["class" => "lead"]);
			$html .= Html::label("Item title");
			$html .= Html::tag("div", Html::input("text", "loan_item_title", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Amount");
			$html .= Html::tag("div", Html::input("text", "loan_amount", $this->getModel()->amount, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Period");
			$html .= Html::tag("div", Html::input("text", "loan_period", $this->getModel()->term, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("First payment");
			$html .= Html::tag("div", Html::input("text", "loan_first_payment", $this->getModel()->first_payment, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Action ID (optional)");
			$html .= Html::tag("div", Html::input("text", "loan_actionId", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Item Type ID (optional)");
			$html .= Html::tag("div", Html::input("text", "loan_itemTypeId", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Contact information", ["class" => "lead"]);
			$html .= Html::label("Contacts mobile");
			$html .= Html::tag("div", Html::input("text", "contacts_mobile", $this->getModel()->person->phone, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Contacts phone");
			$html .= Html::tag("div", Html::input("text", "contacts_phone", $this->getModel()->person->phone, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Contacts email");
			$html .= Html::tag("div", Html::input("text", "contacts_email", $this->getModel()->person->email, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Contacts extra name");
			$html .= Html::tag("div", Html::input("text", "contacts_extra_name", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Contacts extra phone");
			$html .= Html::tag("div", Html::input("text", "contacts_extra_phone", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Contacts extra email");
			$html .= Html::tag("div", Html::input("text", "contacts_extra_email", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Work information", ["class" => "lead"]);
			$html .= Html::label("Work title");
			$html .= Html::tag("div", Html::input("text", "work_title", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Work experience monthes");
			$html .= Html::tag("div", Html::input("text", "work_experience_monthes", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Work position");
			$html .= Html::tag("div", Html::input("text", "work_position", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Work phone");
			$html .= Html::tag("div", Html::input("text", "work_phone", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Work address");
			$html .= Html::tag("div", Html::input("text", "work_address", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Work industry");
			$html .= Html::tag("div", Html::input("text", "work_industry", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Family information", ["class" => "lead"]);
			$html .= Html::label("Family status");
			$html .= Html::tag("div", Html::input("text", "family_status", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Family persons");
			$html .= Html::tag("div", Html::input("text", "family_persons", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Finance information", ["class" => "lead"]);
			$html .= Html::label("Finance net salary");
			$html .= Html::tag("div", Html::input("text", "finance_net_salary", $this->getModel()->person->income, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Finance income family");
			$html .= Html::tag("div", Html::input("text", "finance_income_family", 0, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Finance income other");
			$html .= Html::tag("div", Html::input("text", "finance_income_other", 0, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Finance expense mortgage");
			$html .= Html::tag("div", Html::input("text", "finance_expense_mortgage", 0, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Finance expense home");
			$html .= Html::tag("div", Html::input("text", "finance_expense_home", 0, ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Finance expense other");
			$html .= Html::tag("div", Html::input("text", "finance_expense_other", 0, ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Passport information", ["class" => "lead"]);
			$html .= Html::label("Passport series");
			$html .= Html::tag("div", Html::input("text", "passport_series", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Passport number");
			$html .= Html::tag("div", Html::input("text", "passport_number", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Passport expire date");
			$html .= Html::tag("div", Html::input("text", "passport_expire_date", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Passport issue date");
			$html .= Html::tag("div", Html::input("text", "passport_issue_date", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Passport issue place");
			$html .= Html::tag("div", Html::input("text", "passport_issue_place", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Residence declared information", ["class" => "lead"]);
			$html .= Html::label("Residence declared city");
			$html .= Html::tag("div", Html::input("text", "residence_declared_city", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence declared region");
			$html .= Html::tag("div", Html::input("text", "residence_declared_region", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence declared street");
			$html .= Html::tag("div", Html::input("text", "residence_declared_street", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence declared house number");
			$html .= Html::tag("div", Html::input("text", "residence_declared_house_number", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence declared flat number");
			$html .= Html::tag("div", Html::input("text", "residence_declared_flat_number", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence declared post index");
			$html .= Html::tag("div", Html::input("text", "residence_declared_post_index", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence declared residence status");
			$html .= Html::tag("div", Html::input("text", "residence_declared_residence_status", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Residence real information", ["class" => "lead"]);
			$html .= Html::label("Residence real city");
			$html .= Html::tag("div", Html::input("text", "residence_real_city", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence real region");
			$html .= Html::tag("div", Html::input("text", "residence_real_region", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence real street");
			$html .= Html::tag("div", Html::input("text", "residence_real_street", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence real house number");
			$html .= Html::tag("div", Html::input("text", "residence_real_house_number", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence real flat number");
			$html .= Html::tag("div", Html::input("text", "residence_real_flat_number", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence real post index");
			$html .= Html::tag("div", Html::input("text", "residence_real_post_index", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Residence real residence status");
			$html .= Html::tag("div", Html::input("text", "residence_real_residence_status", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Comments", ["class" => "lead"]);
			$html .= Html::label("Comment");
			$html .= Html::tag("div", Html::textarea("comment", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Post data'), ['class' => 'btn btn-primary btn-block', 'id' => 'incredit-post']), ["class" => "form-group"]);
			$html .= Html::endForm();
			$html .= Html::tag("div", "", ["id" => "incredit-api-info", "class" => "hide"]);
		}		
		return $html;
	}
	
	private function setActions() {
		if (\Yii::$app->getRequest()->post("increditPost") == "true") {
			return $this->_postData();
		}
	}

	private function _postData() {
		ob_end_clean();
		ob_start();
		
		$xml = "";
		$xml .="<request>";
		$xml .="<person>";
		$xml .="<name>".\Yii::$app->getRequest()->post("peron_name")."</name>";
		$xml .="<surname>".\Yii::$app->getRequest()->post("person_surname")."</surname>";
		$xml .="<pk>".\Yii::$app->getRequest()->post("person_pk")."</pk>";
		$xml .="</person>";
		$xml .="<loan>";
		$xml .="<item_title>".\Yii::$app->getRequest()->post("loan_item_title")."</item_title>";
		$xml .="<amount>".\Yii::$app->getRequest()->post("loan_amount")."</amount>";
		$xml .="<period>".\Yii::$app->getRequest()->post("loan_period")."</period>";
		$xml .="<first_payment>".\Yii::$app->getRequest()->post("loan_first_payment")."</first_payment>";
		$xml .="<actionId>".\Yii::$app->getRequest()->post("loan_actionId")."</actionId>";
		$xml .="<itemTypeId>".\Yii::$app->getRequest()->post("loan_itemTypeId")."</itemTypeId>";
		$xml .="</loan>";
		$xml .="<contacts>";
		$xml .="<main>";
		$xml .="<mobile>".\Yii::$app->getRequest()->post("contacts_mobile")."</mobile>";
		$xml .="<phone>".\Yii::$app->getRequest()->post("contacts_phone")."</phone>";
		$xml .="<email>".\Yii::$app->getRequest()->post("contacts_email")."</email>";
		$xml .="</main>";
		$xml .="<extra>";
		$xml .="<name>".\Yii::$app->getRequest()->post("contacts_extra_name")."</name>";
		$xml .="<phone>".\Yii::$app->getRequest()->post("contacts_extra_phone")."</phone>";
		$xml .="<email>".\Yii::$app->getRequest()->post("contacts_extra_email")."</email>";
		$xml .="</extra>";
		$xml .="</contacts>";
		$xml .="<work>";
		$xml .="<title>".\Yii::$app->getRequest()->post("work_title")."</title>";
		$xml .="<experience_monthes>".\Yii::$app->getRequest()->post("work_experience_monthes")."</experience_monthes>";
		$xml .="<position>".\Yii::$app->getRequest()->post("work_position")."</position>";
		$xml .="<phone>".\Yii::$app->getRequest()->post("work_phone")."</phone>";
		$xml .="<address>".\Yii::$app->getRequest()->post("work_address")."</address>";
		$xml .="<industry>".\Yii::$app->getRequest()->post("work_industry")."</industry>";
		$xml .="</work>";
		$xml .="<family>";
		$xml .="<status>".\Yii::$app->getRequest()->post("family_status")."</status>";
		$xml .="<persons>".\Yii::$app->getRequest()->post("family_persons")."</persons>";
		$xml .="</family>";
		$xml .="<finance>";
		$xml .="<net_salary>".\Yii::$app->getRequest()->post("finance_net_salary")."</net_salary>";
		$xml .="<income_family>".\Yii::$app->getRequest()->post("finance_income_family")."</income_family>";
		$xml .="<income_other>".\Yii::$app->getRequest()->post("finance_income_other")."</income_other>";
		$xml .="<expense_mortgage>".\Yii::$app->getRequest()->post("finance_expense_mortgage")."</expense_mortgage>";
		$xml .="<expense_home>".\Yii::$app->getRequest()->post("finance_expense_home")."</expense_home>";
		$xml .="<expense_other>".\Yii::$app->getRequest()->post("finance_expense_other")."</expense_other>";
		$xml .="</finance>";
		$xml .="<passport>";
		$xml .="<series>".\Yii::$app->getRequest()->post("passport_series")."</series>";
		$xml .="<number>".\Yii::$app->getRequest()->post("passport_number")."</number>";
		$xml .="<expire_date>".\Yii::$app->getRequest()->post("passport_expire_date")."</expire_date>";
		$xml .="<issue_date>".\Yii::$app->getRequest()->post("passport_issue_date")."</issue_date>";
		$xml .="<issue_place>".\Yii::$app->getRequest()->post("passport_issue_place")."</issue_place>";
		$xml .="</passport>";
		$xml .="<residence>";
		$xml .="<declared>";
		$xml .="<city>".\Yii::$app->getRequest()->post("residence_declared_city")."</city>";
		$xml .="<region>".\Yii::$app->getRequest()->post("residence_declared_region")."</region>";
		$xml .="<street>".\Yii::$app->getRequest()->post("residence_declared_street")."</street>";
		$xml .="<house_number>".\Yii::$app->getRequest()->post("residence_declared_house_number")."</house_number>";
		$xml .="<flat_number>".\Yii::$app->getRequest()->post("residence_declared_flat_number")."</flat_number>";
		$xml .="<post_index>".\Yii::$app->getRequest()->post("residence_declared_post_index")."</post_index>";
		$xml .="<residence_status>".\Yii::$app->getRequest()->post("residence_declared_residence_status")."</residence_status>";
		$xml .="</declared>";
		$xml .="<real>";
		$xml .="<city>".\Yii::$app->getRequest()->post("residence_real_city")."</city>";
		$xml .="<region>".\Yii::$app->getRequest()->post("residence_real_region")."</region>";
		$xml .="<street>".\Yii::$app->getRequest()->post("residence_real_street")."</street>";
		$xml .="<house_number>".\Yii::$app->getRequest()->post("residence_real_house_number")."</house_number>";
		$xml .="<flat_number>".\Yii::$app->getRequest()->post("residence_real_flat_number")."</flat_number>";
		$xml .="<post_index>".\Yii::$app->getRequest()->post("residence_real_post_index")."</post_index>";
		$xml .="<residence_status>".\Yii::$app->getRequest()->post("residence_real_residence_status")."</residence_status>";
		$xml .="</real>";
		$xml .="</residence>";
		$xml .="<comment>".\Yii::$app->getRequest()->post("comment")."</comment>";
		$xml .="</request>";
		
		$ArrRequest = [];
		$ArrRequest['request'] = "new_request";
		$ArrRequest['login'] = \Yii::$app->params["increditapi_login"];
		$ArrRequest['password'] = \Yii::$app->params["increditapi_pass"];
		$ArrRequest['orgid'] = \Yii::$app->params["increditapi_orgid"];
		$ArrRequest['xml'] = $xml;
		$ArrRequest['md5'] = md5($ArrRequest['xml']);
		
		$HttpRequest = http_build_query($ArrRequest);
		
		$Context_options = [
				'http' => [
						'method' => 'POST',
						'timeout' => '30',
						'header' => "Content-type: application/x-www-form-urlencoded\r\n",
						"content-Length:". strlen($HttpRequest)."\R\n",
						'content' => $HttpRequest
				]
		];
		
		$XmlResponce = @file_get_contents(\Yii::$app->params["increditapi_url"], null, stream_context_create($Context_options));

		$this->_log("RESONSE - ".$XmlResponce);
		
		$response = new \SimpleXMLElement($XmlResponce);
		$content = "No valid response data";
		$valid = false;
		//error
		if ((property_exists($response, "error"))) {
			$content = "Error: ".$response->error->title;
		}
		// success
		if ((property_exists($response, "request_uid"))) {
			$content = "Successfully sent data, contract_id: ".$response->request_uid;
			$this->_setContractId($response->request_uid);
			$valid = true;
		}
		
		echo json_encode(["html" => $content, "valid" => $valid]);
		
		\Yii::$app->end();
	}

	private function _setContractId($id) {
		$this->_log("SET_CONTRACT_ID - CONTRACT_ID - ".$id." - MODEL_ID - ".$this->getModel()->id);
	
		\Yii::$app->db->createCommand("INSERT INTO `incredit_api` (`id`, `loan_id`, `contract_id`) VALUES (NULL, {$this->getModel()->id}, {$id});")->execute();
	}
	
	private function _getContractId() {
		if ($data = \Yii::$app->db->createCommand("SELECT *  FROM `incredit_api` WHERE `loan_id` = ".$this->getModel()->id)->queryOne()) {
			return $data["contract_id"];
		}
	
		return null;
	}
	
	private function _log($msg) {
		$fd = fopen(\Yii::$app->params["increditapi_log_path"], "a+");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $str . "\n");
		fclose($fd);
	}
}

class IncreditAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $css = [
	];

	public $js = [
			'js/incredit.js?v=2',
	];

	public $depends = [
			'yii\web\YiiAsset',
			'yii\bootstrap\BootstrapAsset',
	];
}