<?php
namespace app\components\api;

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use yii\web\AssetBundle;
use yii\web\UploadedFile;
use app\models\Changes;
use app\models\Loan;

class TFBankApi extends ApiModel {

	public function __construct($model) {
		$this->setModel($model);
		$this->setTitle("TFBank api");
		$this->setContent($this->_getContent());
		$this->setActions();
		TFBankAsset::register(\Yii::$app->view);
	}
	
	private function _getContent() {
		$html = "<h2>".$this->getLabel()."</h2>";
		
		$html .= "<div class='alert alert-danger hide'><p>Vēl nelielas izmaiņas veicu, ap 11:00 varēs sūtīt tālāk!</p></div>";
		
		
		if ($data = $this->_getContractId()) {
			$html .= "<div class='alert alert-info'><p>Data already sent, contract id: ".$data['contract_id'].", status: ".$data['status'].".</p></div>";
			$html .= Html::beginForm("", "", ["id" => "tfbank-form", "enctype" => "multipart/form-data"]);
			$html .= Html::input("hidden", "tfbankPostUpload", "true", ["class" => "form-control"]);
			
			$html .= Html::label("Type");
			$html .= Html::tag("div", '<select class="form-control" name="type" required="required">
<option value="SignedPromissoryNote">SignedPromissoryNote</option>
<option value="IdCard">IdCard</option>
<option value="TaxReturn">TaxReturn</option>
<option value="BankStatement">BankStatement</option>
<option value="EmploymentCertificate">EmploymentCertificate</option>
</select>', ["class" => "form-group"]);
			
			$html .= Html::label("File");
			$html .= Html::tag("div", Html::input("file", "file", "", ["class" => "form-control"]), ["class" => "form-group"]);
			
			
			$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Send file'), ['class' => 'btn btn-primary btn-block', 'id' => 'tfbank-post2']), ["class" => "form-group"]);
			$html .= Html::endForm();
			$html .= Html::tag("div", "", ["id" => "tfbank-api-info", "class" => "hide"]);
			
		} else {
			$html .= Html::beginForm("", "", ["id" => "tfbank-form"]);
			$html .= Html::input("hidden", "tfbankPost", "true", ["class" => "form-control"]);
			
			$html .= '<div class="row">';
			$html .= '<div class="col-xs-12 col-md-12">';
			
			$html .= Html::tag("p", "Information", ["class" => "lead"]);
			$html .= Html::label("Amount");
			$html .= Html::tag("div", '<input class="form-control" name="am" value="'.$this->getModel()->amount.'">', ["class" => "form-group"]);
			
			$html .= Html::label("Term");
			
			$html .= Html::tag("div", '<select class="form-control" name="tm" required="required">
<option value="12" '.($this->getModel()->term == 12 ? "selected='selected'" : "").'>12</option>
<option value="24" '.($this->getModel()->term == 24 ? "selected='selected'" : "").'>24</option>
<option value="36" '.($this->getModel()->term == 36 ? "selected='selected'" : "").'>36</option>
<option value="46" '.($this->getModel()->term == 46 ? "selected='selected'" : "").'>46</option>
<option value="60" '.($this->getModel()->term == 60 ? "selected='selected'" : "").'>60</option>
</select>', ["class" => "form-group"]);
			
			$html .= Html::tag("p", "Accomodation", ["class" => "lead"]);
			$html .= Html::label("Type");
			$html .= Html::tag("div", '<select class="form-control" name="act" required="required">
<option value="Other">Other</option>
<option value="Renter">Renter</option>
<option value="Tenant">Tenant</option>
<option value="PropertyOwnedByFamily">PropertyOwnedByFamily</option>
</select>', ["class" => "form-group"]);
			
			
			$html .= '</div>';
			
			$html .= '<div class="col-xs-12 col-md-6">';

			
			$html .= Html::tag("p", "Deklarētā adrese", ["class" => "lead"]);
			$html .= Html::label("City");
			$html .= Html::tag("div", '<select class="form-control" name="city" required="required">
<option value="Rīga">Rīga</option>
<option value="Daugavpils">Daugavpils</option>
<option value="Jēkabpils">Jēkabpils</option>
<option value="Jelgava">Jelgava</option>
<option value="Jūrmala">Jūrmala</option>
<option value="Liepāja">Liepāja</option>
<option value="Rēzekne">Rēzekne</option>
<option value="Valmiera">Valmiera</option>
<option value="Ventspils">Ventspils</option>
<option value="Aglonas">Aglonas novads</option>
<option value="Aizkraukles">Aizkraukles novads</option>
<option value="Aizputes novads">Aizputes novads</option>
<option value="Aknīstes novads">Aknīstes novads</option>
<option value="Alojas novads">Alojas novads</option>
<option value="Alsungas novads">Alsungas novads</option>
<option value="Alūksnes novads">Alūksnes novads</option>
<option value="Amatas novads">Amatas novads</option>
<option value="Apes novads">Apes novads</option>
<option value="Auces novads">Auces novads</option>
<option value="Ādažu novads">Ādažu novads</option>
<option value="Babītes novads">Babītes novads</option>
<option value="Baldones novads">Baldones novads</option>
<option value="Baltinavas novads">Baltinavas novads</option>
<option value="Balvu novads">Balvu novads</option>
<option value="Bauskas novads">Bauskas novads</option>
<option value="Beverīnas novads">Beverīnas novads</option>
<option value="Brocēnu novads">Brocēnu novads</option>
<option value="Burtnieku novads">Burtnieku novads</option>
<option value="Carnikavas novads">Carnikavas novads</option>
<option value="Cēsu novads">Cēsu novads</option>
<option value="Cesvaines novads">Cesvaines novads</option>
<option value="Ciblas novads">Ciblas novads</option>
<option value="Dagdas novads">Dagdas novads</option>
<option value="Daugavpils novads">Daugavpils novads</option>
<option value="Dobeles novads">Dobeles novads</option>
<option value="Dundagas novads">Dundagas novads</option>
<option value="Durbes novads">Durbes novads</option>
<option value="Engures novads">Engures novads</option>
<option value="Ērgļu novads">Ērgļu novads</option>
<option value="Garkalnes novads">Garkalnes novads</option>
<option value="Grobiņas novads">Grobiņas novads</option>
<option value="Gulbenes novads">Gulbenes novads</option>
<option value="Iecavas novads">Iecavas novads</option>
<option value="Ikšķiles novads">Ikšķiles novads</option>
<option value="Inčukalna novads">Inčukalna novads</option>
<option value="Ilūkstes novads">Ilūkstes novads</option>
<option value="Jaunjelgavas novads">Jaunjelgavas novads</option>
<option value="Jaunpiebalgas novads">Jaunpiebalgas novads</option>
<option value="Jaunpils novads">Jaunpils novads</option>
<option value="Jēkabpils novads">Jēkabpils novads</option>
<option value="Jelgavas novads">Jelgavas novads</option>
<option value="Kandavas novads">Kandavas novads</option>
<option value="Kārsavas novads">Kārsavas novads</option>
<option value="Kokneses novads">Kokneses novads</option>
<option value="Krāslavas novads">Krāslavas novads</option>
<option value="Krimuldas novads">Krimuldas novads</option>
<option value="Krustpils novads">Krustpils novads</option>
<option value="Kuldīgas novads">Kuldīgas novads</option>
<option value="Ķeguma novads">Ķeguma novads</option>
<option value="Ķekavas novads">Ķekavas novads</option>
<option value="Lielvārdes novads">Lielvārdes novads</option>
<option value="Līgatnes novads">Līgatnes novads</option>
<option value="Limbažu novads">Limbažu novads</option>
<option value="Līvānu novads">Līvānu novads</option>
<option value="Lubānas novads">Lubānas novads</option>
<option value="Ludzas novads">Ludzas novads</option>
<option value="Madonas novads">Madonas novads</option>
<option value="Mālpils novads">Mālpils novads</option>
<option value="Mārupes novads">Mārupes novads</option>
<option value="Mazsalacas novads">Mazsalacas novads</option>
<option value="Naukšēnu novads">Naukšēnu novads</option>
<option value="Neretas novads">Neretas novads</option>
<option value="Nīcas novads">Nīcas novads</option>
<option value="Ogres novads">Ogres novads</option>
<option value="Olaines novads">Olaines novads</option>
<option value="Ozolnieku novads">Ozolnieku novads</option>
<option value="Pārgaujas novads">Pārgaujas novads</option>
<option value="Pāvilostas novads">Pāvilostas novads</option>
<option value="Pļaviņu novads">Pļaviņu novads</option>
<option value="Preiļu novads">Preiļu novads</option>
<option value="Priekules novads">Priekules novads</option>
<option value="Priekuļu novads">Priekuļu novads</option>
<option value="Raunas novads">Raunas novads</option>
<option value="Rēzeknes novads">Rēzeknes novads</option>
<option value="Riebiņu novads">Riebiņu novads</option>
<option value="Rojas novads">Rojas novads</option>
<option value="Ropažu novads">Ropažu novads</option>
<option value="Rucavas novads">Rucavas novads</option>
<option value="Rugāju novads">Rugāju novads</option>
<option value="Rundāles novads">Rundāles novads</option>
<option value="Rūjienas novads">Rūjienas novads</option>
<option value="Salacgrīvas novads">Salacgrīvas novads</option>
<option value="Salas novads">Salas novads</option>
<option value="Salaspils novads">Salaspils novads</option>
<option value="Saldus novads">Saldus novads</option>
<option value="Saulkrastu novads">Saulkrastu novads</option>
<option value="Sējas novads">Sējas novads</option>
<option value="Siguldas novads">Siguldas novads</option>
<option value="Skrīveru novads">Skrīveru novads</option>
<option value="Skrundas novads">Skrundas novads</option>
<option value="Smiltenes novads">Smiltenes novads</option>
<option value="Stopiņu novads">Stopiņu novads</option>
<option value="Strenču novads">Strenču novads</option>
<option value="Talsu novads">Talsu novads</option>
<option value="Tērvetes novads">Tērvetes novads</option>
<option value="Tukuma novads">Tukuma novads</option>
<option value="Vaiņodes novads">Vaiņodes novads</option>
<option value="Valkas novads">Valkas novads</option>
<option value="Valmieras novads">Valmieras novads</option>
<option value="Varakļānu novads">Varakļānu novads</option>
<option value="Vārkavas novads">Vārkavas novads</option>
<option value="Vecpiebalgas novads">Vecpiebalgas novads</option>
<option value="Vecumnieku novads">Vecumnieku novads</option>
<option value="Ventspils novads">Ventspils novads</option>
<option value="Viesītes novads">Viesītes novads</option>
<option value="Viļakas novads">Viļakas novads</option>
<option value="Viļānu novads">Viļānu novads</option>
<option value="Zilupes novads">Zilupes novads</option>
</select>', ["class" => "form-group"]);
			
			$html .= Html::label("Street");
			$html .= Html::tag("div", Html::input("text", "street", ($this->getModel()->extra ? $this->getModel()->extra->car_owner_address : null), ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Zip");
			$html .= Html::tag("div", Html::input("text", "zip", $this->getZip(($this->getModel()->extra ? $this->getModel()->extra->car_owner_address : null)), ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= '</div>';
			$html .= '<div class="col-xs-12 col-md-6">';
			$html .= Html::tag("p", "Faktiskā adrese", ["class" => "lead"]);
			
			$html .= Html::label("City");
			$html .= Html::tag("div", '<select class="form-control" name="city2" required="required">
<option value="Rīga">Rīga</option>
<option value="Daugavpils">Daugavpils</option>
<option value="Jēkabpils">Jēkabpils</option>
<option value="Jelgava">Jelgava</option>
<option value="Jūrmala">Jūrmala</option>
<option value="Liepāja">Liepāja</option>
<option value="Rēzekne">Rēzekne</option>
<option value="Valmiera">Valmiera</option>
<option value="Ventspils">Ventspils</option>
<option value="Aglonas">Aglonas novads</option>
<option value="Aizkraukles">Aizkraukles novads</option>
<option value="Aizputes novads">Aizputes novads</option>
<option value="Aknīstes novads">Aknīstes novads</option>
<option value="Alojas novads">Alojas novads</option>
<option value="Alsungas novads">Alsungas novads</option>
<option value="Alūksnes novads">Alūksnes novads</option>
<option value="Amatas novads">Amatas novads</option>
<option value="Apes novads">Apes novads</option>
<option value="Auces novads">Auces novads</option>
<option value="Ādažu novads">Ādažu novads</option>
<option value="Babītes novads">Babītes novads</option>
<option value="Baldones novads">Baldones novads</option>
<option value="Baltinavas novads">Baltinavas novads</option>
<option value="Balvu novads">Balvu novads</option>
<option value="Bauskas novads">Bauskas novads</option>
<option value="Beverīnas novads">Beverīnas novads</option>
<option value="Brocēnu novads">Brocēnu novads</option>
<option value="Burtnieku novads">Burtnieku novads</option>
<option value="Carnikavas novads">Carnikavas novads</option>
<option value="Cēsu novads">Cēsu novads</option>
<option value="Cesvaines novads">Cesvaines novads</option>
<option value="Ciblas novads">Ciblas novads</option>
<option value="Dagdas novads">Dagdas novads</option>
<option value="Daugavpils novads">Daugavpils novads</option>
<option value="Dobeles novads">Dobeles novads</option>
<option value="Dundagas novads">Dundagas novads</option>
<option value="Durbes novads">Durbes novads</option>
<option value="Engures novads">Engures novads</option>
<option value="Ērgļu novads">Ērgļu novads</option>
<option value="Garkalnes novads">Garkalnes novads</option>
<option value="Grobiņas novads">Grobiņas novads</option>
<option value="Gulbenes novads">Gulbenes novads</option>
<option value="Iecavas novads">Iecavas novads</option>
<option value="Ikšķiles novads">Ikšķiles novads</option>
<option value="Inčukalna novads">Inčukalna novads</option>
<option value="Ilūkstes novads">Ilūkstes novads</option>
<option value="Jaunjelgavas novads">Jaunjelgavas novads</option>
<option value="Jaunpiebalgas novads">Jaunpiebalgas novads</option>
<option value="Jaunpils novads">Jaunpils novads</option>
<option value="Jēkabpils novads">Jēkabpils novads</option>
<option value="Jelgavas novads">Jelgavas novads</option>
<option value="Kandavas novads">Kandavas novads</option>
<option value="Kārsavas novads">Kārsavas novads</option>
<option value="Kokneses novads">Kokneses novads</option>
<option value="Krāslavas novads">Krāslavas novads</option>
<option value="Krimuldas novads">Krimuldas novads</option>
<option value="Krustpils novads">Krustpils novads</option>
<option value="Kuldīgas novads">Kuldīgas novads</option>
<option value="Ķeguma novads">Ķeguma novads</option>
<option value="Ķekavas novads">Ķekavas novads</option>
<option value="Lielvārdes novads">Lielvārdes novads</option>
<option value="Līgatnes novads">Līgatnes novads</option>
<option value="Limbažu novads">Limbažu novads</option>
<option value="Līvānu novads">Līvānu novads</option>
<option value="Lubānas novads">Lubānas novads</option>
<option value="Ludzas novads">Ludzas novads</option>
<option value="Madonas novads">Madonas novads</option>
<option value="Mālpils novads">Mālpils novads</option>
<option value="Mārupes novads">Mārupes novads</option>
<option value="Mazsalacas novads">Mazsalacas novads</option>
<option value="Naukšēnu novads">Naukšēnu novads</option>
<option value="Neretas novads">Neretas novads</option>
<option value="Nīcas novads">Nīcas novads</option>
<option value="Ogres novads">Ogres novads</option>
<option value="Olaines novads">Olaines novads</option>
<option value="Ozolnieku novads">Ozolnieku novads</option>
<option value="Pārgaujas novads">Pārgaujas novads</option>
<option value="Pāvilostas novads">Pāvilostas novads</option>
<option value="Pļaviņu novads">Pļaviņu novads</option>
<option value="Preiļu novads">Preiļu novads</option>
<option value="Priekules novads">Priekules novads</option>
<option value="Priekuļu novads">Priekuļu novads</option>
<option value="Raunas novads">Raunas novads</option>
<option value="Rēzeknes novads">Rēzeknes novads</option>
<option value="Riebiņu novads">Riebiņu novads</option>
<option value="Rojas novads">Rojas novads</option>
<option value="Ropažu novads">Ropažu novads</option>
<option value="Rucavas novads">Rucavas novads</option>
<option value="Rugāju novads">Rugāju novads</option>
<option value="Rundāles novads">Rundāles novads</option>
<option value="Rūjienas novads">Rūjienas novads</option>
<option value="Salacgrīvas novads">Salacgrīvas novads</option>
<option value="Salas novads">Salas novads</option>
<option value="Salaspils novads">Salaspils novads</option>
<option value="Saldus novads">Saldus novads</option>
<option value="Saulkrastu novads">Saulkrastu novads</option>
<option value="Sējas novads">Sējas novads</option>
<option value="Siguldas novads">Siguldas novads</option>
<option value="Skrīveru novads">Skrīveru novads</option>
<option value="Skrundas novads">Skrundas novads</option>
<option value="Smiltenes novads">Smiltenes novads</option>
<option value="Stopiņu novads">Stopiņu novads</option>
<option value="Strenču novads">Strenču novads</option>
<option value="Talsu novads">Talsu novads</option>
<option value="Tērvetes novads">Tērvetes novads</option>
<option value="Tukuma novads">Tukuma novads</option>
<option value="Vaiņodes novads">Vaiņodes novads</option>
<option value="Valkas novads">Valkas novads</option>
<option value="Valmieras novads">Valmieras novads</option>
<option value="Varakļānu novads">Varakļānu novads</option>
<option value="Vārkavas novads">Vārkavas novads</option>
<option value="Vecpiebalgas novads">Vecpiebalgas novads</option>
<option value="Vecumnieku novads">Vecumnieku novads</option>
<option value="Ventspils novads">Ventspils novads</option>
<option value="Viesītes novads">Viesītes novads</option>
<option value="Viļakas novads">Viļakas novads</option>
<option value="Viļānu novads">Viļānu novads</option>
<option value="Zilupes novads">Zilupes novads</option>
</select>', ["class" => "form-group"]);
			$html .= Html::label("Street");
			$html .= Html::tag("div", Html::input("text", "street2", ($this->getModel()->extra ? $this->getModel()->extra->car_owner_address : null), ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Zip");
			$html .= Html::tag("div", Html::input("text", "zip2", $this->getZip(($this->getModel()->extra ? $this->getModel()->extra->car_owner_address : null)), ["class" => "form-control"]), ["class" => "form-group"]);
			
			$html .= '</div>';
			
			$html .= '<div class="col-xs-12 col-md-12">';
			$html .= '<div class="checkbox" style="margin-bottom:20px;"> <label> <input type="checkbox" name="hasPropery" id="tfbank-has">Pieder īpašums?</label> </div>';
			$html .= '</div>';
			
			$html .= '<div class="col-xs-12 col-md-12" id="tfbank-has-target" style="display:none;">';
			$html .= Html::tag("p", "Īpašuma adrese", ["class" => "lead"]);
			
			$html .= Html::tag("div", '<select class="form-control" name="city3" required="required">
<option value="Rīga">Rīga</option>
<option value="Daugavpils">Daugavpils</option>
<option value="Jēkabpils">Jēkabpils</option>
<option value="Jelgava">Jelgava</option>
<option value="Jūrmala">Jūrmala</option>
<option value="Liepāja">Liepāja</option>
<option value="Rēzekne">Rēzekne</option>
<option value="Valmiera">Valmiera</option>
<option value="Ventspils">Ventspils</option>
<option value="Aglonas">Aglonas novads</option>
<option value="Aizkraukles">Aizkraukles novads</option>
<option value="Aizputes novads">Aizputes novads</option>
<option value="Aknīstes novads">Aknīstes novads</option>
<option value="Alojas novads">Alojas novads</option>
<option value="Alsungas novads">Alsungas novads</option>
<option value="Alūksnes novads">Alūksnes novads</option>
<option value="Amatas novads">Amatas novads</option>
<option value="Apes novads">Apes novads</option>
<option value="Auces novads">Auces novads</option>
<option value="Ādažu novads">Ādažu novads</option>
<option value="Babītes novads">Babītes novads</option>
<option value="Baldones novads">Baldones novads</option>
<option value="Baltinavas novads">Baltinavas novads</option>
<option value="Balvu novads">Balvu novads</option>
<option value="Bauskas novads">Bauskas novads</option>
<option value="Beverīnas novads">Beverīnas novads</option>
<option value="Brocēnu novads">Brocēnu novads</option>
<option value="Burtnieku novads">Burtnieku novads</option>
<option value="Carnikavas novads">Carnikavas novads</option>
<option value="Cēsu novads">Cēsu novads</option>
<option value="Cesvaines novads">Cesvaines novads</option>
<option value="Ciblas novads">Ciblas novads</option>
<option value="Dagdas novads">Dagdas novads</option>
<option value="Daugavpils novads">Daugavpils novads</option>
<option value="Dobeles novads">Dobeles novads</option>
<option value="Dundagas novads">Dundagas novads</option>
<option value="Durbes novads">Durbes novads</option>
<option value="Engures novads">Engures novads</option>
<option value="Ērgļu novads">Ērgļu novads</option>
<option value="Garkalnes novads">Garkalnes novads</option>
<option value="Grobiņas novads">Grobiņas novads</option>
<option value="Gulbenes novads">Gulbenes novads</option>
<option value="Iecavas novads">Iecavas novads</option>
<option value="Ikšķiles novads">Ikšķiles novads</option>
<option value="Inčukalna novads">Inčukalna novads</option>
<option value="Ilūkstes novads">Ilūkstes novads</option>
<option value="Jaunjelgavas novads">Jaunjelgavas novads</option>
<option value="Jaunpiebalgas novads">Jaunpiebalgas novads</option>
<option value="Jaunpils novads">Jaunpils novads</option>
<option value="Jēkabpils novads">Jēkabpils novads</option>
<option value="Jelgavas novads">Jelgavas novads</option>
<option value="Kandavas novads">Kandavas novads</option>
<option value="Kārsavas novads">Kārsavas novads</option>
<option value="Kokneses novads">Kokneses novads</option>
<option value="Krāslavas novads">Krāslavas novads</option>
<option value="Krimuldas novads">Krimuldas novads</option>
<option value="Krustpils novads">Krustpils novads</option>
<option value="Kuldīgas novads">Kuldīgas novads</option>
<option value="Ķeguma novads">Ķeguma novads</option>
<option value="Ķekavas novads">Ķekavas novads</option>
<option value="Lielvārdes novads">Lielvārdes novads</option>
<option value="Līgatnes novads">Līgatnes novads</option>
<option value="Limbažu novads">Limbažu novads</option>
<option value="Līvānu novads">Līvānu novads</option>
<option value="Lubānas novads">Lubānas novads</option>
<option value="Ludzas novads">Ludzas novads</option>
<option value="Madonas novads">Madonas novads</option>
<option value="Mālpils novads">Mālpils novads</option>
<option value="Mārupes novads">Mārupes novads</option>
<option value="Mazsalacas novads">Mazsalacas novads</option>
<option value="Naukšēnu novads">Naukšēnu novads</option>
<option value="Neretas novads">Neretas novads</option>
<option value="Nīcas novads">Nīcas novads</option>
<option value="Ogres novads">Ogres novads</option>
<option value="Olaines novads">Olaines novads</option>
<option value="Ozolnieku novads">Ozolnieku novads</option>
<option value="Pārgaujas novads">Pārgaujas novads</option>
<option value="Pāvilostas novads">Pāvilostas novads</option>
<option value="Pļaviņu novads">Pļaviņu novads</option>
<option value="Preiļu novads">Preiļu novads</option>
<option value="Priekules novads">Priekules novads</option>
<option value="Priekuļu novads">Priekuļu novads</option>
<option value="Raunas novads">Raunas novads</option>
<option value="Rēzeknes novads">Rēzeknes novads</option>
<option value="Riebiņu novads">Riebiņu novads</option>
<option value="Rojas novads">Rojas novads</option>
<option value="Ropažu novads">Ropažu novads</option>
<option value="Rucavas novads">Rucavas novads</option>
<option value="Rugāju novads">Rugāju novads</option>
<option value="Rundāles novads">Rundāles novads</option>
<option value="Rūjienas novads">Rūjienas novads</option>
<option value="Salacgrīvas novads">Salacgrīvas novads</option>
<option value="Salas novads">Salas novads</option>
<option value="Salaspils novads">Salaspils novads</option>
<option value="Saldus novads">Saldus novads</option>
<option value="Saulkrastu novads">Saulkrastu novads</option>
<option value="Sējas novads">Sējas novads</option>
<option value="Siguldas novads">Siguldas novads</option>
<option value="Skrīveru novads">Skrīveru novads</option>
<option value="Skrundas novads">Skrundas novads</option>
<option value="Smiltenes novads">Smiltenes novads</option>
<option value="Stopiņu novads">Stopiņu novads</option>
<option value="Strenču novads">Strenču novads</option>
<option value="Talsu novads">Talsu novads</option>
<option value="Tērvetes novads">Tērvetes novads</option>
<option value="Tukuma novads">Tukuma novads</option>
<option value="Vaiņodes novads">Vaiņodes novads</option>
<option value="Valkas novads">Valkas novads</option>
<option value="Valmieras novads">Valmieras novads</option>
<option value="Varakļānu novads">Varakļānu novads</option>
<option value="Vārkavas novads">Vārkavas novads</option>
<option value="Vecpiebalgas novads">Vecpiebalgas novads</option>
<option value="Vecumnieku novads">Vecumnieku novads</option>
<option value="Ventspils novads">Ventspils novads</option>
<option value="Viesītes novads">Viesītes novads</option>
<option value="Viļakas novads">Viļakas novads</option>
<option value="Viļānu novads">Viļānu novads</option>
<option value="Zilupes novads">Zilupes novads</option>
</select>', ["class" => "form-group"]);
			$html .= Html::label("Street");
			$html .= Html::tag("div", Html::input("text", "street3", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= Html::label("Zip");
			$html .= Html::tag("div", Html::input("text", "zip3", "", ["class" => "form-control"]), ["class" => "form-group"]);
			$html .= '</div>';
			
			$html .= '</div>';
			
		//	if ($this->getModel()->extra && $this->getModel()->extra->bank_account_nr) {
				$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Post data'), ['class' => 'btn btn-primary btn-block', 'id' => 'tfbank-post']), ["class" => "form-group"]);
		//	} else {
		//		$html .= "<p>No bank account nr, please provide it.</p>";
		//		$html .= Html::tag("div", Html::submitButton(\Yii::t('app/field', 'Post data'), ['class' => 'btn btn-primary btn-block', 'id' => 'tfbank-post', "disabled" => "disabled"]), ["class" => "form-group"]);
		//	}
			
			$html .= Html::endForm();
			$html .= Html::tag("div", "", ["id" => "tfbank-api-info", "class" => "hide"]);
		}		
		return $html;
	}
	
	private function setActions() {
		if (\Yii::$app->getRequest()->post("tfbankPost") == "true") {
			return $this->_postData();
		}
		if (\Yii::$app->getRequest()->post("tfbankPostUpload") == "true") {
			return $this->_postDataUpload();
		}
	}
	
	private function getZip($address) {
		if (!$address) {
			return null;
		}
		return substr(strrchr($address, 'LV-'), 0, 7);
	}
	
	private function _postDataUpload() {
		
		
		$client = new \SoapClient(\Yii::$app->params["tfbank_url"], array('soap_version' => SOAP_1_1));

		$file = UploadedFile::getInstanceByName("file");
		
		$params = array(
				'ApplicationId' => $this->_getContractId()["contract_id"],
				'Content' => file_get_contents($file->tempName),
				'ContentType' => $file->type,
				'DocumentType' => \Yii::$app->getRequest()->post("type"),
				'FileName' => $file->name
		);
		
		
		$ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
		$AuthHeader = new AuthHeader(\Yii::$app->params["tfbank_username"], \Yii::$app->params["tfbank_password"]);

		
		$objVar_Session_Inside = new \SoapVar($AuthHeader->PrintHeader(), XSD_ANYXML, null, null, null);
		$actionHeader = new \SoapHeader($ns, 'Security', $objVar_Session_Inside, false);
		$client->__setSoapHeaders($actionHeader);
		
		try {
			$valid = 1;
			$result = $client->UploadDocument(array('request'=>$params));
			$content = ('Document '.$result->UploadDocumentResult->FileReceived.' Has been successfully uploaded.');
		} catch (SoapFault $fault) {
			$valid = 0;
			$content = "SOAP server returned the following validation ERROR: $fault->faultcode-$fault->faultstring<BR/><BR/>";
			if(($fault->detail!=null)&&($fault->detail->ValidationFault!=null)) {
				$content .= $fault->detail->ValidationFault->Message;
			}
		}
		if ($valid) {
			\Yii::$app->getSession()->setFlash("success", \Yii::t("app/mail", "File sent!"));
		} else {
			\Yii::$app->getSession()->setFlash("danger", \Yii::t("app/mail", "File not sent! Error uploading file."));
		}
		
		//return \Yii::$app->getResponse()->refresh();
	}
	
	private function closest($array, $number)
	{
	    
	    sort($array);
	    foreach ($array as $a) {
	        if ($a >= $number) return $a;
	    }
	    
	    return end($array); // or return NULL;
	}
	
	private function _postData() {
		ob_end_clean();
		ob_start();
		
		$months_values = array(12,24,36,46,60);
		$api_term = $this->closest($months_values, \Yii::$app->getRequest()->post("tm"));
		
		$client = new \SoapClient(\Yii::$app->params["tfbank_url"], array('soap_version' => SOAP_1_1));
		if (\Yii::$app->getRequest()->post("hasPropery")) {
			$propParams = [
					'PropertyOwner' => true,
					'OwnedPropertyAddress' => array(
						'Street' => 'MyOwnedPropertyStreet 1',
						'City' => 'MyOwnedPropertyCity',
						'Zip' => 'LV-13999'
					),
			];
		} else {
			$propParams = [
					'PropertyOwner' => false
			];
		}
		$params = array(
				//'ActivityCode'=>'ACTIVITY_CODE_PHP', //done
				'Affiliate' => 'OneFinance-2002-10001', //done
				'Amount' => \Yii::$app->getRequest()->post("am"), //done
				'Applicant' => array(
						'Accommodation' => array(
								'AdultsInFamily' => null, //done
								'ChildrenInFamily' => $this->getModel()->person->dependants, //done
								'Since' => null, //done
								'Type' => \Yii::$app->getRequest()->post("act")//done
						),
						'Address' => array(
								'Street' => \Yii::$app->getRequest()->post("street"), //done
								'City' => \Yii::$app->getRequest()->post("city"), //done
								'Zip' => \Yii::$app->getRequest()->post("zip") //done
						),
						'ContactAddress' => array(
								'Street' => \Yii::$app->getRequest()->post("street2"), //done
								'City' => \Yii::$app->getRequest()->post("city2"), //done
								'Zip' => \Yii::$app->getRequest()->post("zip2") //done
						),
						'ContactAddressDiffers' => true,
						'Contacts' => array(
								'CellPhone' => $this->getModel()->person->phone,
								'Email' => $this->getModel()->person->email,
						),
						'Employment' =>array(
								'EmployedSince' => $this->getWorkExp(),
								'EmployerName' => ($this->getModel()->extra ? $this->getModel()->extra->car_workplace : null),
								'EmployerPhone' => null,
								'Income' => 12*$this->getModel()->person->income,
								'OccupationType' => 'Permanent'
						),
						'Loans' => array(
								'OtherMonthlyCost' => $this->getModel()->person->outcome,
								//'OtherTotal' => 
						),
						$propParams,
						'PersonalInfo' => array(
								'FirstName' => $this->getModel()->person->name, //done
								'Gender' => $this->getGender(), //done
								'LastName' => $this->getModel()->person->surname, //done
								'MaritalStatus' => $this->getMaritalStatus(), //done
								'Ssn' => $this->getModel()->person->personal_code //done
						),
				),
				'BankInfo' => array(
						'Iban' => ($this->getModel()->extra ? "LV66BANK0000000000000" : "LV66BANK0000000000000") //done
				),
				'Channel' => 'ExternalPartner', //done
				'ClientIp' => $this->getModel()->ip_ountry, //done
				'ExternalPartner' => 'OneFinance', //done
				'GeneratePromissoryNote' => false, //done
				'Product' => 'LvaCashLoan', //done
				'RepaymentPeriod' => $api_term //done
		);
		
		$ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
		$AuthHeader = new AuthHeader(\Yii::$app->params["tfbank_username"], \Yii::$app->params["tfbank_password"]);
		///var_dump($AuthHeader->PrintHeader());
		//exit;
		
		$objVar_Session_Inside = new \SoapVar($AuthHeader->PrintHeader(), XSD_ANYXML, null, null, null);
		$actionHeader = new \SoapHeader($ns, 'Security', $objVar_Session_Inside, false);
		$client->__setSoapHeaders($actionHeader);
		
		try {
			$result = $client->RegisterApplication(array('request'=>$params));
			
			$this->_log("Result - ".json_encode($result->RegisterApplicationResult));
			
			if ($result->RegisterApplicationResult->Errors) {
				$valid = 0;
				$content = $result->RegisterApplicationResult->Errors->Error->Message;
			} else {
				if ($result->RegisterApplicationResult->ApplicationNumber) {
					$valid = 1;
					$this->_setContractId($result->RegisterApplicationResult->ApplicationNumber, $result->RegisterApplicationResult->Decision);
					$content = 'Application registered with Number='.$result->RegisterApplicationResult->ApplicationNumber .', Decision='.$result->RegisterApplicationResult->Decision;
					
					$desc = "TFBank sent. Response:
 <b>Decision=".$result->RegisterApplicationResult->Decision."</b>
 Number=".$result->RegisterApplicationResult->ApplicationNumber."
 ApprovedAmount=".$result->RegisterApplicationResult->ApprovedAmount."
 ApprovedRepaymentPeriod=".$result->RegisterApplicationResult->ApprovedRepaymentPeriod."
 InsuranceCost=".$result->RegisterApplicationResult->InsuranceCost."
 InterestRate=".$result->RegisterApplicationResult->InterestRate."
 MonthlyCost=".$result->RegisterApplicationResult->MonthlyCost."
 MonthlyFee=".$result->RegisterApplicationResult->MonthlyFee."
 StartFee=".$result->RegisterApplicationResult->StartFee."";
					
					$mod = $this->getModel();
					$mod->description = $desc;
					$mod->save();
				} else {
					$valid = 0;
				}
			}
		} catch (\SoapFault $fault) {
			$valid = 0;
			$content = "SOAP server returned the following validation ERROR: ".$fault->faultcode." - ".$fault->faultstring;
			if((@$fault->detail!=null)&&(@$fault->detail->ValidationFault!=null)) {
				$content .= " SOAP FAULT: ".$fault->detail->ValidationFault->Message;
			}
		}
		
		echo json_encode(["html" => $content, "valid" => $valid, "valid" => $valid]);
		
		\Yii::$app->end();
	}

	private function _setContractId($id, $status) {
		$this->_log("SET_CONTRACT_ID - CONTRACT_ID - ".$id." - ".$status." - MODEL_ID - ".$this->getModel()->id);
	
		\Yii::$app->db->createCommand("INSERT INTO `tfbank_api` (`id`, `loan_id`, `contract_id`, `status`) VALUES (NULL, {$this->getModel()->id}, {$id}, '{$status}');")->execute();
	}
	
	private function _getContractId() {
		if ($data = \Yii::$app->db->createCommand("SELECT *  FROM `tfbank_api` WHERE `loan_id` = ".$this->getModel()->id)->queryOne()) {
			return $data;
		}
	
		return null;
	}
	/**
	 * 	    		0 => "Neprecējies/Neprecējusies",
	    		1 => "Faktiskā kopdzīve",
	    		2 => "Precējies/Precējusies",
	    		3 => "Šķīries/Šķīrusies",
	    		4 => "Atraitnis/Atraitne",
	 * @return string
	 */
	private function getMaritalStatus() {
		if ($this->getModel()->person->family_status == null) {
			return "Unknown";
		}
		if ($this->getModel()->person->family_status == 0) {
			return "Single";
		}
		if ($this->getModel()->person->family_status == 1) {
			return "Cohabitee";
		}
		if ($this->getModel()->person->family_status == 2) {
			return "Married";
		}
		if ($this->getModel()->person->family_status == 3) {
			return "Divorced";
		}
		if ($this->getModel()->person->family_status == 4) {
			return "Single";
		}
		
		return "Unknown";
	}
	
	private function getGender() {
		if ($this->getModel()->person->gender == "f") {
			return "Female";
		}
		if ($this->getModel()->person->gender == "m") {
			return "Male";
		}
		
		return "Unknown";
	}
	
	private function getWorkExp() {
		if ($this->getModel()->extra) {
			if ($this->getModel()->extra->car_workplace) {
				$time = @strtotime("-".$this->getModel()->extra->car_workplace." months");
				if ($time) {
					return date("c", $time);
				}
			}
		}
		
		return null;
	}
	
	private function _log($msg) {
		$fd = fopen(\Yii::$app->params["tfbankapi_log_path"], "a+");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $str . "\n");
		fclose($fd);
	}
}

class TFBankAsset extends AssetBundle {
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $css = [
	];

	public $js = [
			'js/tfbank.js?v=4',
	];

	public $depends = [
			'yii\web\YiiAsset',
			'yii\bootstrap\BootstrapAsset',
	];
}

function closest($array, $number)
{
	
	sort($array);
	foreach ($array as $a) {
		if ($a >= $number) return $a;
	}
	
	return end($array); // or return NULL;
}

class AuthHeader {
	private $Username;
	private $Password;

	function __construct($user,$pwd) {
		$this->Username = $user;
		$this->Password = $pwd;
	}

	function PrintHeader() {
		$created = new \DateTime(null);
		//$created = $created->add(new \DateInterval('PT1H'));
		
		$expires = new \DateTime(null);
		//$expires = $expires->add(new \DateInterval('PT1H'));
		$expires = $expires->add(new \DateInterval('PT5M'));
		
		return '<o:Security  xmlns:o="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
			<u:Timestamp>
				<u:Created>'.gmdate('Y-m-d\TH:i:s\Z',$created->getTimestamp()).'</u:Created>
				<u:Expires>'.gmdate('Y-m-d\TH:i:s\Z',$expires->getTimestamp()).'</u:Expires>
			</u:Timestamp>
			<o:UsernameToken>
				<o:Username>'.$this->Username.'</o:Username>
				<o:Password>'.$this->Password.'</o:Password>
			</o:UsernameToken>
		</o:Security>';
	}
}
