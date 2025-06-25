<?php
namespace app\components\api;

use app\models\Changes;
use app\models\Loan;
use yii\bootstrap\Html;

class EfinanceEmailApi extends ApiModel {

	public function __construct($model) {
		$this->setModel($model);
		$this->setTitle("Iespeja api");
		$this->setContent($this->_getContent());
		
		if (\Yii::$app->getRequest()->get("task") == "efinance-email") {
			$this->_post();
			return \Yii::$app->getResponse()->redirect(["loan/view", "id" => $model->id]);
		}
	}
	
	private function _getContent() {
		$html = "<h2>".$this->getLabel()."</h2>";
		if ($this->getModel()->extra && $this->getModel()->extra->bank_account_statement) {
			$sent = Changes::find()->where(["type_id" => $this->getModel()->id, "type" => Loan::TYPE, "attr" => "actions_40"])->one();
			if ($sent) {
				$html .= "<p>Already sent data.</p>";
			} else {
				$html .= Html::beginForm("?task=efinance-email", "get", ["id" => ""]);
				$html .= "<div class='form-group'><label>Amount</label><input class='form-control' name='am' value='".$this->getModel()->amount."'></div>";
				$html .= "<div class='form-group'><label>Term</label><input class='form-control' name='tm' value='".$this->getModel()->term."'></div>";
				$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Send data'), ['class' => 'btn btn-primary btn-block']), ["class" => "form-group"]);
				$html .= Html::endForm();
			}
		} else {
			$html .= "<p>No bank account statement</p>";
		}
		return $html;
	}
	
	private function _post() {
		$sent = Changes::find()->where(["type_id" => $this->getModel()->id, "type" => Loan::TYPE, "attr" => "actions_40"])->one();
		if (!$sent) {
			$change = new Changes();
			$change->attr = "actions_40";
			$change->type = Loan::TYPE;
			$change->type_id = $this->getModel()->id;
			$change->attr_from = 0;
			$change->attr_to = 1;
			$change->create_time = time();
			$change->save();
			
			$text = "";
			$text .= "Name: ".$this->getModel()->person->name."<br />";
			$text .= "Lastname: ".$this->getModel()->person->surname."<br />";
			$text .= "Personal Code: ".$this->getModel()->person->personal_code."<br />";
			$text .= "Income: ".$this->getModel()->person->income."<br />";
			$text .= "Outcome: ".$this->getModel()->person->outcome."<br />";
			$text .= "Loan amount: ".\Yii::$app->getRequest()->get("am")."<br />";
			$text .= "Term: ".\Yii::$app->getRequest()->get("tm")."<br />";
			
			\Yii::$app->getMailer()->setTransport(\Yii::$app->params["mailer"]["onefinance"]);
				
			$mailer = \Yii::$app->mailer->compose()
    			->setFrom('')
    			->setTo("")
    			->setSubject("Pieteikums - ".$this->getModel()->person->personal_code)
    			->setHtmlBody($text)
    			->setTextBody(strip_tags($text));
    			 
    			$file = "https://server.lv/file?=".str_replace("$", "%24", $this->getModel()->extra->bank_account_statement)."&f_preview=yes";
    			
			$mailer->attachContent(file_get_contents($file), ['fileName' => 'attach.pdf', 'contentType' => 'application/pdf']);
    			
    		if ($mailer->send()) {
    			
    		}
		}
	}
}
