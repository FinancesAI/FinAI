<?php
namespace app\components\api;

use yii\web\AssetBundle;

class AizdevumsApi extends ApiModel {

	public function __construct($model) {
		$this->setModel($model);
		$this->setTitle("Aizdevums api");
		$this->setContent($this->_getContent());
		AizdevumsAsset::register(\Yii::$app->view);
		
		if (\Yii::$app->getRequest()->isPost) {
			$this->_post();
		}
	}
	
	private function _post() {
		if (!isset($_POST['action'])) {
			return;
		}
		
		ob_end_clean();
		ob_start();

		if (\Yii::$app->getRequest()->post("task") == "aizdevums-file") {
			
			header('Content-type: application/pdf');
			$pdf = $_POST['pdf'];
			$binary = base64_decode($pdf);
			echo $binary;
			$content = ob_get_contents();
			ob_end_clean();
			
			echo $content;
			
			\Yii::$app->end();
		}
		
		//Autorizēšanās dati
		$login = \Yii::$app->params["aizdevumsapi_login"];
		$password = \Yii::$app->params["aizdevumsapi_pass"];
		
		//wsdl glabāšanas ceļš ar faila nosaukumu, pēc noklusējuma tajā pašā mapē, kur projekts
		$save_wsdl_path = __DIR__.'/wsdl.xml';
		
		$wsdl = \Yii::$app->params["aizdevumsapi_url"];
		
		$service = new Ntlmservice($wsdl, [
				'trace' => 1,
				'login' => $login,
				'password' => $password,
				'url' => $wsdl,
				'local_path' => $save_wsdl_path,
				'cache_wsdl' => WSDL_CACHE_NONE, //nekesojam wsdl, ja gadījumā tas mainās
		]);

		//Ja action ir CREATEWEBAPPLICATION, tikai, tad iekļaujam failu data.php, citos gadījumos nav nepieciešams
		if (isset($_POST['action']) && !empty($_POST['action']) && ($_POST['action'] == 'CREATEWEBAPPLICATION')) {

			//Ziņas par aizņēmēju
			$name = \Yii::$app->getRequest()->post("a_vards");        //Klienta Vārds
			$name2 = \Yii::$app->getRequest()->post('a_uzvards');       //Klienta Uzvārds
			$regNo = \Yii::$app->getRequest()->post('RegNo');        //Klienta Personas kods / Reģistrācijas Nr.
			$passportNo = \Yii::$app->getRequest()->post('AppNo');       //Klienta Pases Nr. / Apliecības Nr.
			$applType = \Yii::$app->getRequest()->post("aizpilditajs");      //1=Aizņēmējs; 0=Galvotājs
			$juridical = \Yii::$app->getRequest()->post("aizpilditaja_tips");    //1=Juridiska; 0=Fiziska
			$MainName = \Yii::$app->getRequest()->post("kred_njem_vards");    //1=Personas, par kuru galvo, vārds
			$MainName2 = \Yii::$app->getRequest()->post("kred_njem_uzvards");    //1=Personas, par kuru galvo, uzvārds
			$Signer = \Yii::$app->getRequest()->post("Signer");    //
			$SignerID = \Yii::$app->getRequest()->post("SignerID");    //
			$Reason = \Yii::$app->getRequest()->post("Reason");    //
			//Informācija par aizdevumu
			$contractGroupCode = \Yii::$app->getRequest()->post("kredita_veids");   //NAUDA;CITS;AUTO NAUDA=Naudas kredīts; CITS=Kredīts pirkumiem un pakalpojumiem; AUTO=Auto kredīts
			$amount = \Yii::$app->getRequest()->post("pirkuma_summa");      //Nepieciešamā aizdevuma summa EUR (t.sk.PVN)
			$monthQuantity = \Yii::$app->getRequest()->post("menesi");      //Vēlamais aizdevuma termiņš
			$itemName = \Yii::$app->getRequest()->post("prece");       //Preces nosaukums
			$ChassisNo = \Yii::$app->getRequest()->post("ChassisNo");       //Preces nosaukums
			//$prefDueDate = \Yii::$app->getRequest()->post(""); 							//pirmā maksājuma datums
			//Faktiskā dzīvesvietas adrese
			$city = \Yii::$app->getRequest()->post("a_pagpil");        //Pagasts/pilsēta 	(Faktiskā)
			$street = \Yii::$app->getRequest()->post("a_iela");        //Iela 				(Faktiskā)
			$homeNoName = \Yii::$app->getRequest()->post("a_maja");       //Mājas Nr./nosaukums  (Faktiskais)
			$flatNo = \Yii::$app->getRequest()->post("a_dzivoklis");      //Dzīvokļa Nr.  	(Faktiskais)
			$postCode = \Yii::$app->getRequest()->post("a_indekss");      //Pasta indekss  	(Faktiskais)
			$areaCodeActual = \Yii::$app->getRequest()->post("a_rajons");     //Novads 			(Faktiskais)
			//Pieraksta vai deklarētā adrese
			$city2 = \Yii::$app->getRequest()->post("a2_pagpil");       //Pagasts/pilsēta 	(Deklarētā)
			$street2 = \Yii::$app->getRequest()->post("a2_iela");       //Iela 				(Deklarētā)
			$homeNoName2 = \Yii::$app->getRequest()->post("a2_maja");      //Mājas Nr./nosaukums (Deklarētais)
			$flatNo2 = \Yii::$app->getRequest()->post("a2_dzivoklis");      //Dzīvokļa Nr. 		(Deklarētais)
			$postCode2 = \Yii::$app->getRequest()->post("a2_indekss");      //Pasta indekss 	(Deklarētais)
			$areaCodeFormal = \Yii::$app->getRequest()->post("a2_rajons");     //Novads 			(Deklarētais)
			//Kontaktinformācija
			$phoneNo = \Yii::$app->getRequest()->post("a_maju_tel");      //Mājas tālruņa Nr.
			$mobPhoneNo = \Yii::$app->getRequest()->post("a_mob_tel");      //Mobilā tālruņa Nr.
			$workPhoneNo = \Yii::$app->getRequest()->post("a_darba_tel");     //Darba tālruņa Nr.
			$email = \Yii::$app->getRequest()->post("Email");        //E-pasts
			//Darbavieta
			$workPlace = \Yii::$app->getRequest()->post("a_darbavieta");     //Darba vieta
			$workPosition = \Yii::$app->getRequest()->post("a_amats");      //Amats
			$workExp = \Yii::$app->getRequest()->post("a_stazs");       //Stāžš
			$salary = \Yii::$app->getRequest()->post("a_neto");        //Neto ienākumi
			//Pieteikuma iesniedzēja dzīvesbiedre/s
			$adName = \Yii::$app->getRequest()->post("m_vards");       //Vārds					Pieteikuma iesniedzēja dzīvesbiedre/s
			$adName2 = \Yii::$app->getRequest()->post("m_uzvards");       //Uzvārds 				Pieteikuma iesniedzēja dzīvesbiedre/s
			$adMobPhone = \Yii::$app->getRequest()->post("m_mob_tel");      //Mobilā tālruņa Nr. 	Pieteikuma iesniedzēja dzīvesbiedre/s
			$adWorkPhone = \Yii::$app->getRequest()->post("m_darba_tel");     //Darba tālruņa Nr. 	Pieteikuma iesniedzēja dzīvesbiedre/s
			$adWorkPlace = \Yii::$app->getRequest()->post("m_darbavieta");     //Darba vieta 			Pieteikuma iesniedzēja dzīvesbiedre/s
			$adWorkPosition = \Yii::$app->getRequest()->post("m_amats");     //Amats 				Pieteikuma iesniedzēja dzīvesbiedre/s
			$AdWorkExp = \Yii::$app->getRequest()->post("m_stazs");     //Amats 				Pieteikuma iesniedzēja dzīvesbiedre/s
			$AdSalary = \Yii::$app->getRequest()->post("m_neto");     //Amats 				Pieteikuma iesniedzēja dzīvesbiedre/s
			//Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			$familyName = \Yii::$app->getRequest()->post("c_vards");      //Vārds 				1.Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			$familyName2 = \Yii::$app->getRequest()->post("c_uzvards");      //Uzvārds				1. Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			$familyWorkPlace = \Yii::$app->getRequest()->post("c_darbavieta");    //Darba vieta			1. Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			$family2Name = \Yii::$app->getRequest()->post("c2_vards");      //Vārds					2. Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			$family2Name2 = \Yii::$app->getRequest()->post("c2_uzvards");     //Uzvārds				2. Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			$family2WorkPlace = \Yii::$app->getRequest()->post("c2_darbavieta");   //Darba vieta			2. Ziņas par ģimenes locekļiem, ar kuriem dzīvo kopā
			//Kontaktpersona aizņēmēja prombūtnē
			$contactName = \Yii::$app->getRequest()->post("k_vards");      //Vārds					Kontaktpersona aizņēmēja prombūtnē (izņemot dzīvesbiedru)
			$contactName2 = \Yii::$app->getRequest()->post("k_uzvards");     //Uzvārds				Kontaktpersona aizņēmēja prombūtnē (izņemot dzīvesbiedru)
			$contactPhone = \Yii::$app->getRequest()->post("k_tel");      ////Mobilā tālruņa Nr.	Kontaktpersona aizņēmēja prombūtnē (izņemot dzīvesbiedru)
			//Paša un ģimenes kopējais saistības (banku kredīti, līzingi, citas saistības)
			$debtName = \Yii::$app->getRequest()->post("kred_iestade");      //Iestāeds nosaukums
			$debtMonthlyPayment = \Yii::$app->getRequest()->post("men_maks");    //Ikmēneša maksājums
			$debtMonths = \Yii::$app->getRequest()->post("atlik_men");     //Atlikušo mēnešu skaits
			$comment = \Yii::$app->getRequest()->post("Comment");       //Komentārs
			//VSAA
			$VSAACheck = \Yii::$app->getRequest()->post("VSAACheck");
			$VSAASalaryCheck = \Yii::$app->getRequest()->post("VSAASalaryCheck");
			$VSAAPensionCheck = \Yii::$app->getRequest()->post("VSAAPensionCheck");
			
			
			$WebApplicationDebts = array(
					"DebtName" => $debtName,
					"DebtMonthlyPayment" => $debtMonthlyPayment,
					"DebtMonths" => $debtMonths
			);
			
			$today = date("Y-m-d");
			
			$WebApplications = array(
					"PartnerCode" => \Yii::$app->params["aizdevumsapi_code"], //unikāls lauks, informāciju pieprasīt mārketinga nodaļā.
					"ContractDate" => $today, //pietiekuma datums
					"ApplType" => $applType,
					"Juridical" => $juridical,
					"MainName" => $MainName,
					"MainName2" => $MainName2,
					"Signer" => $Signer,
					"SignerID" => $SignerID,
					"Reason" => $Reason,
					"Name" => $name,
					"Name2" => $name2,
					"RegNo" => $regNo,
					"PassportNo" => $passportNo,
					"AreaCodeActual" => $areaCodeActual,
					"City" => $city,
					"Street" => $street,
					"HomeNoName" => $homeNoName,
					"FlatNo" => $flatNo,
					"PostCode" => $postCode,
					"AreaCodeFormal" => $areaCodeFormal,
					"City2" => $city2,
					"Street2" => $street2,
					"HomeNoName2" => $homeNoName2,
					"FlatNo2" => $flatNo2,
					"PostCode2" => $postCode2,
					"PhoneNo" => $phoneNo,
					"MobPhoneNo" => $mobPhoneNo,
					"WorkPhoneNo" => $workPhoneNo,
					"Email" => $email,
					"WorkPlace" => $workPlace,
					"WorkPosition" => $workPosition,
					"WorkExp" => $workExp,
					"Salary" => $salary,
					"AdName" => $adName,
					"AdName2" => $adName2,
					"AdMobPhone" => $adMobPhone,
					"AdWorkPhone" => $adWorkPhone,
					"AdWorkPlace" => $adWorkPlace,
					"AdWorkPosition" => $adWorkPosition,
					"AdWorkExp" => $AdWorkExp,
					"AdSalary" => $AdSalary,
					"FamilyName" => $familyName,
					"FamilyName2" => $familyName2,
					"FamilyWorkPlace" => $familyWorkPlace,
					"Family2Name" => $family2Name,
					"Family2Name2" => $family2Name2,
					"Family2WorkPlace" => $family2WorkPlace,
					"ContactName" => $contactName,
					"ContactName2" => $contactName2,
					"ContactPhone" => $contactPhone,
					"ContractGroupCode" => $contractGroupCode,
					"ItemName" => $itemName,
					"ChassisNo" => $ChassisNo,
					"Amount" => $amount,
					"FirstPayment" => '0',
					"PrefDueDate" => '1',
					"MonthQuantity" => $monthQuantity,
					"WebApplicationDebts" => $WebApplicationDebts,
					"comment" => $comment,
					"VSAACheck" => $VSAACheck,
					"VSAASalaryCheck" => $VSAASalaryCheck,
					"VSAAPensionCheck" => $VSAAPensionCheck
			);
			
			$WebApplications = array(
					"WebApplication" => $WebApplications,
					"Response" => array(
							"Status" => '',
							"ContractNoORStatus" => '',
							"Comment" => '',
							"MonthPayment" => '',
							"InterestRate" => '',
							"FirstPayment" => '',
							"ContractFee" => ''
					)
			);
			
			$webApplicationXML = array(
					"webApplicationXML" => $WebApplications
			);
		}
		//$action == null;
		//Pārbaudam vai ir visas postotās vērtības
		if (isset($_POST['action']) && !empty($_POST['action']) && (isset($_POST['appNumber'])) && (isset($_POST['secNo'])) && (isset($_POST['contractNo'])) && (isset($_POST['webAppNo']))
		) {

			$action = $_POST['action'];
			$appNumber = (int) $_POST['appNumber'];
			$secNo = $_POST['secNo'];
			$contractNo = $_POST['contractNo'];
			$webAppNo = $_POST['webAppNo'];

			//Atbilstoši no saņemtā action izsaucam atbilstošo funkciju
			switch ($action) {
				case 'CREATEWEBAPPLICATION' :
					CreateWebApplication($service, $webApplicationXML, $this);
					break;
				case 'CHECKSTATUS' :
					CheckStatus($service, $appNumber);
					break;
				case 'UPLOADCONTRACT' :
					UploadContract($service, $secNo);
					break;
				case 'CONTRACTSIGNED' :
					ContractSigned($service, $contractNo);
					break;
				case 'CANCELAPPLICATION' :
					CancelApplication($service, $webAppNo);
					break;
			}
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		$json = json_decode($content);
		
		if ($action == "CREATEWEBAPPLICATION") {
			if ($json->valid) {
				$this->_setContractId($json->nr);
			}
		}
		
		if ($action == "CHECKSTATUS") {
			if ((strpos($json->status, \Yii::$app->params["aizdevumsapi_code"]) !== false)) {
				$this->_setContractNr($json->status);
			}
		}
		
		if ($action == "CANCELAPPLICATION") {
			if (strtolower($json->status) == "yes") {
				$this->_deleteContract();
			}
		}
		
		echo $content;
		
		\Yii::$app->end();
	}
	
	private function _getContent() {
		$html = "<h2>".$this->getLabel()."</h2>";
		$contractId = $this->_getContractId();
		if ($contractId) {
			$contractNr = $this->_getContractNr();
			$html .= "<p>Data already sent, contract id: ".$this->_getContractId()."</p>";
			$html .= '<form class="form-horizontal" role="form">
                <div class="form-group">
                    <div class="col-sm-4 hide">
                        <input type="text" class="form-control" id="appNumber" name="appNumber" value="'.$contractId.'" placeholder="XXXXXXXX">
                    </div>
                    <div class="col-sm-12">  
                        <input type="button" class="btn btn-primary btn-block" id="CHECKSTATUS" onclick="callFunction(this.id);" name="CHECKSTATUS"
                               value="Check Status"/>
                    </div>      
                </div>';
				
				
					$html .= '<div class="'.($contractNr ? "" : "hide").'"><div class="form-group">
                    <div class="col-sm-4 hide">
                        <input type="text" class="form-control" id="contractNoAndSocSecNo" name="contractNoAndSocSecNo" placeholder="TESTSS-XXXXXX-XXXXX-X/XXXXXX-XXXXXX" value="'.$contractNr.'/221086-10618">
                    </div>
                    <div class="col-sm-12">  
                        <input type="button" class="btn btn-primary btn-block" id="UPLOADCONTRACT" onclick="callFunction(this.id);" name="UPLOADCONTRACT"
                               value="Get contract"/>
                    </div>      
                </div>
                <div class="form-group">
                    <div class="col-sm-4 hide">
                        <input type="text" class="form-control" id="contractNo" maxlength="30" name="contractNo" placeholder="TESTSS-XXXXXX-XXXXX-X" value="'.$contractNr.'">
                    </div>
                    <div class="col-sm-12">  
                        <input type="button" class="btn btn-primary btn-block" id="CONTRACTSIGNED" onclick="callFunction(this.id);" name="CONTRACTSIGNED"
                               value="Cotract Signed"/>
                    </div>      
                </div></div>';
			
                $html .= '<div class="form-group">
                    <div class="col-sm-4 hide">
                        <input type="text" class="form-control" id="webAppNo" type="text" name="webAppNo" placeholder="XXXXXXXX" value="'.$contractId.'">
                    </div>
                    <div class="col-sm-12">  
                        <input type="button" class="btn btn-primary btn-block" id="CANCELAPPLICATION" onclick="callFunction(this.id);" name="CANCELAPPLICATION"
                               value="Cancel Applicaion"/>
                    </div>      
            </form>            </div><p id="loading"></p>
                <p id="info"></p>';
		} else {
			$rnn = null;
			$rns = null;
			
			$relative = ($this->getModel()->extra ? $this->getModel()->extra->car_owner : null);
			if ($relative) {
				$parts = explode(" ", $relative);
				$rnn = $parts[0];
				if (isset($parts[1])) {
					$rns = $parts[1];
				}
				if (isset($parts[2])) {
					$rns .= " ".$parts[2];
				}
				if (isset($parts[3])) {
					$rns .= " ".$parts[3];
				}
				if (isset($parts[4])) {
					$rns .= " ".$parts[4];
				}
			}
			
			$rnr = ($this->getModel()->extra ? ltrim($this->getModel()->extra->car_phone, "+371") : null);
			
			$html .= '<div class="aizdevums-forma">
			<form class="" id="aizdevums-form" data-toggle="validator" name="forma" role="form" action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="CREATEWEBAPPLICATION"/>
     			<input type="hidden" name="appNumber" value=""/>
   				<input type="hidden" name="secNo" value=""/>
       			<input type="hidden" name="contractNo" value=""/>
           		<input type="hidden" name="webAppNo" value=""/>
				<input type="radio" class="hidden radio aizpilditajs" name="aizpilditajs" value="1" checked="checked">
					
				<p class="lead">Informācija par aizdevumu</p>
					
				<div class="form-group">
               		<label for="type_select">Veids</label>
                    	<select class="form-control" name="kredita_veids" id="type_select" data-error="Šis ir obligāti aizpildāms lauks." required>
                        	<option value="NAUDA">Naudas kredīts</option>
                        	<option value="PĀRKREDITĀCIJA NAUDA">Kredīta apvienošana</option>
                          	<option value="CITS">Kredīts pirkumiem un pakalpojumiem</option>
                      		<option value="AUTO">Auto kredīts</option>
							<option value="AUTO30">Transports + 30 dienas</option>
                       	</select>
                 	<div class="help-block with-errors"></div>
             	</div>
				<div class="form-group">
               		<label for="crTerm">Termiņš</label>
                   	<select class="form-control obligate" name="menesi" id="crTerm" value="" required>
                  		<option value="3"'.($this->getModel()->term == 3 ? " selected=selected" : "").'>3 mēneši</option>
                        <option value="6"'.($this->getModel()->term == 6 ? " selected=selected" : "").'>6 mēneši</option>
                        <option value="12"'.($this->getModel()->term == 12 ? " selected=selected" : "").'>12 mēneši</option>
                  		<option value="18"'.($this->getModel()->term == 18 ? " selected=selected" : "").'>18 mēneši</option>
                 		<option value="24"'.($this->getModel()->term == 24 ? " selected=selected" : "").'>24 mēneši</option>
                      	<option value="36"'.($this->getModel()->term == 36 ? " selected=selected" : "").'>36 mēneši</option>
                    	<option value="48"'.($this->getModel()->term == 48 ? " selected=selected" : "").'>48 mēneši</option>
                        <option value="60"'.($this->getModel()->term == 60 ? " selected=selected" : "").'>60 mēneši</option>
                       	<option value="72"'.($this->getModel()->term == 72 ? " selected=selected" : "").'>72 mēneši</option>
                       	<option value="84"'.($this->getModel()->term == 84 ? " selected=selected" : "").'>84 mēneši</option>
                	</select>
        		</div>
				<div class="form-group has-feedback">
             		<label for="pirkuma_summa">Aizdevuma summa (t.sk.PVN)</label>
                	<input type="text" id="pirkuma_summa" name="pirkuma_summa" maxlength="9" value="'.$this->getModel()->amount.'" mask="money" class="form-control" data-error="Šis ir obligāti aizpildāms lauks." placeholder="Aizdevuma summa (t.sk.PVN) " tabindex="" required>
                	<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
       				<span class="help-block with-errors"></span>
             	</div>
				<div class="form-group has-feedback">
             		<input type="text" name="prece" id="prece" class="form-control" placeholder="Mērķis" data-error="Šis ir obligāti aizpildāms lauks." tabindex="4" maxlength="60" required>
                 	<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              		<span class="help-block with-errors"></span>
 				</div>
                <div class="form-group has-feedback hide">
                	<input type="text" name="ChassisNo" id="ChassisNo" class="form-control" placeholder="Šasijas numurs" tabindex="4" maxlength="60">
               	</div>
					
				<p class="lead aiznemejs-switch hide">Ziņas par aizņēmēju</p>
				<div class="form-group has-feedback hide">
                	<label for="a_vards">Vārds</label>
                	<input type="text" name="a_vards" id="a_vards" class="form-control" data-minlength="3" placeholder="Klienta vārds" data-error="Šis ir obligāti aizpildāms lauks." value="'.$this->getModel()->person->name.'" maxlength="30" required>
                	<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
               		<span class="help-block with-errors"></span>
              	</div>
				<div class="form-group has-feedback hide">
                 	<label for="a_uzvards">Uzvārds</label>
                	<input type="text" name="a_uzvards" id="a_uzvards" class="form-control" data-minlength="3" data-error="Šis ir obligāti aizpildāms lauks." placeholder="Klienta uzvārds" value="'.$this->getModel()->person->surname.'" maxlength="30" required>
                  	<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                	<span class="help-block with-errors"></span>
              	</div>
				<div class="form-group has-feedback hide">
               		<label for="RegNo" class="fiziska-switch">Personas kods</label>
               		<input type="text" name="RegNo" id="RegNo" class="form-control" pattern="[0-9]{6}-[0-9]{5}" placeholder="Personas kods" data-error="" value="'.$this->getModel()->person->personal_code.'" required>
           			<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
               		<span class="help-block with-errors"></span>
    			</div>
				<div class="form-group hide">
              		<input type="hidden" value="" id="a_pase1" name="a_pase1" data-error="Šis ir obligāti aizpildāms lauks." maxlength="2" required>
                  	<label for="AppNo" class="pase-switch">Pases Nr.</label>
                  	<input type="text" name="AppNo" id="AppNo" class="form-control" data-error="Šis ir obligāti aizpildāms lauks." placeholder="Pases Nr." value="-" maxlength="9" required>
                   	<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                  	<span class="help-block with-errors"></span>
              	</div>
				
				<p class="lead">Faktiskā dzīvesvietas adrese</p>
                <div class="form-group has-feedback">
                	<label for="a_pagpil">Pagasts/pilsēta</label>
                   	<input type="text" name="a_pagpil" id="a_pagpil" data-error="Šis ir obligāi aizpildāms lauks." class="form-control f1" placeholder="Pagasts/pilsēta" value="" maxlength="20" required>
                 	<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                   	<span class="help-block with-errors"></span>
             	</div>
				<div class="form-group">
					<label for="a_rajons">Novads</label>
					<select class="form-control f2" name="a_rajons" id="a_rajons" required>
						<option value=""></option>
						<option value="-------">-------</option>
						<option value="AGLONAS NOVADS">Aglonas novads</option>
						<option value="AIZKRAUKLES NOVADS">Aizkraukles novads</option>
						<option value="AIZPUTES NOVADS">Aizputes novads</option>
						<option value="AKNĪSTES NOVADS">Aknīstes novads</option>
						<option value="ALOJAS NOVADS">Alojas novads</option>
						<option value="ALSUNGAS NOVADS">Alsungas novads</option>
						<option value="ALŪKSNES NOVADS">Alūksnes novads</option>
						<option value="AMATAS NOVADS">Amatas novads</option>
						<option value="APES NOVADS">Apes novads</option>
						<option value="AUCES NOVADS">Auces novads</option>
						<option value="ĀDAŽU NOVADS">Ādažu novads</option>
						<option value="BABĪTES NOVADS">Babītes novads</option>
						<option value="BALDONES NOVADS">Baldones novads</option>
						<option value="BALTINAVAS NOVADS">Baltinavas novads</option>
						<option value="BALVU NOVADS">Balvu novads</option>
						<option value="BAUSKAS NOVADS">Bauskas novads</option>
						<option value="BEVERĪNAS NOVADS">Beverīnas novads</option>
						<option value="BROCĒNU NOVADS">Brocēnu novads</option>
						<option value="BURTNIEKU NOVADS">Burtnieku novads</option>
						<option value="CARNIKAVAS NOVADS">Carnikavas novads</option>
						<option value="CESVAINES NOVADS">Cesvaines novads</option>
						<option value="CĒSU NOVADS">Cēsu novads</option>
						<option value="CIBLAS NOVADS">Ciblas novads</option>
						<option value="DAGDAS NOVADS">Dagdas novads</option>
						<option value="DAUGAVPILS NOVADS">Daugavpils novads</option>
						<option value="DOBELES NOVADS">Dobeles novads</option>
						<option value="DUNDAGAS NOVADS">Dundagas novads</option>
						<option value="DURBES NOVADS">Durbes novads</option>
						<option value="ENGURES NOVADS">Engures novads</option>
						<option value="ĒRGĻU NOVADS">Ērgļu novads</option>
						<option value="GARKALNES NOVADS">Garkalnes novads</option>
						<option value="GROBIŅAS NOVADS">Grobiņas novads</option>
						<option value="GULBENES NOVADS">Gulbenes novads</option>
						<option value="IECAVAS NOVADS">Iecavas novads</option>
						<option value="IKŠĶILES NOVADS">Ikšķiles novads</option>
						<option value="ILŪKSTES NOVADS">Ilūkstes novads</option>
						<option value="INČUKALNA NOVADS">Inčukalna novads</option>
						<option value="JAUNJELGAVAS NOVADS">Jaunjelgavas novads</option>
						<option value="JAUNPIEBALGAS NOVADS">Jaunpiebalgas novads</option>
						<option value="JAUNPILS NOVADS">Jaunpils novads</option>
						<option value="JELGAVAS NOVADS">Jelgavas novads</option>
						<option value="JĒKABPILS NOVADS">Jēkabpils novads</option>
						<option value="KANDAVAS NOVADS">Kandavas novads</option>
						<option value="KĀRSAVAS NOVADS">Kārsavas novads</option>
						<option value="KOCĒNU NOVADS">Kocēnu novads</option>
						<option value="KOKNESES NOVADS">Kokneses novads</option>
						<option value="KRĀSLAVAS NOVADS">Krāslavas novads</option>
						<option value="KRIMULDAS NOVADS">Krimuldas novads</option>
						<option value="KRUSTPILS NOVADS">Krustpils novads</option>
						<option value="KULDĪGAS NOVADS">Kuldīgas novads</option>
						<option value="ĶEGUMA NOVADS">Ķeguma novads</option>
						<option value="ĶEKAVAS NOVADS">Ķekavas novads</option>
						<option value="LIELVĀRDES NOVADS">Lielvārdes novads</option>
						<option value="LIMBAŽU NOVADS">Limbažu novads</option>
						<option value="LĪGATNES NOVADS">Līgatnes novads</option>
						<option value="LĪVĀNU NOVADS">Līvānu novads</option>
						<option value="LUBĀNAS NOVADS">Lubānas novads</option>
						<option value="LUDZAS NOVADS">Ludzas novads</option>
						<option value="MADONAS NOVADS">Madonas novads</option>
						<option value="MAZSALACAS NOVADS">Mazsalacas novads</option>
						<option value="MĀLPILS NOVADS">Mālpils novads</option>
						<option value="MĀRUPES NOVADS">Mārupes novads</option>
						<option value="MĒRSRAGA NOVADS">Mērsraga novads</option>
						<option value="NAUKŠĒNU NOVADS">Naukšēnu novads</option>
						<option value="NERETAS NOVADS">Neretas novads</option>
						<option value="NĪCAS NOVADS">Nīcas novads</option>
						<option value="OGRES NOVADS">Ogres novads</option>
						<option value="OLAINES NOVADS">Olaines novads</option>
						<option value="OZOLNIEKU NOVADS">Ozolnieku novads</option>
						<option value="PĀRGAUJAS NOVADS">Pārgaujas novads</option>
						<option value="PĀVILOSTAS NOVADS">Pāvilostas novads</option>
						<option value="PĻAVIŅU NOVADS">Pļaviņu novJads</option>
						<option value="PREIĻU NOVADS">Preiļu novads</option>
						<option value="PRIEKULES NOVADS">Priekules novads</option>
						<option value="PRIEKUĻU NOVADS">Priekuļu novads</option>
						<option value="RAUNAS NOVADS">Raunas novads</option>
						<option value="RĒZEKNES NOVADS">Rēzeknes novads</option>
						<option value="RIEBIŅU NOVADS">Riebiņu novads</option>
						<option value="RĪGAS NOVADS">Rīgas novads</option>
						<option value="ROJAS NOVADS">Rojas novads</option>
						<option value="ROPAŽU NOVADS">Ropažu novads</option>
						<option value="RUCAVAS NOVADS">Rucavas novads</option>
						<option value="RUGĀJU NOVADS">Rugāju novads</option>
						<option value="RUNDĀLES NOVADS">Rundāles novads</option>
						<option value="RŪJIENAS NOVADS">Rūjienas novads</option>
						<option value="SALACGRĪVAS NOVADS">Salacgrīvas novads</option>
						<option value="SALAS NOVADS">Salas novads</option>
						<option value="SALASPILS NOVADS">Salaspils novads</option>
						<option value="SALDUS NOVADS">Saldus novads</option>
						<option value="SAULKRASTU NOVADS">Saulkrastu novads</option>
						<option value="SĒJAS NOVADS">Sējas novads</option>
						<option value="SIGULDAS NOVADS">Siguldas novads</option>
						<option value="SKRĪVERU NOVADS">Skrīveru novads</option>
						<option value="SKRUNDAS NOVADS">Skrundas novads</option>
						<option value="SMILTENES NOVADS">Smiltenes novads</option>
						<option value="STOPIŅU NOVADS">Stopiņu novads</option>
						<option value="STRENČU NOVADS">Strenču novads</option>
						<option value="TALSU NOVADS">Talsu novads</option>
						<option value="TĒRVETES NOVADS">Tērvetes novads</option>
						<option value="TUKUMA NOVADS">Tukuma novads</option>
						<option value="VAIŅODES NOVADS">Vaiņodes novads</option>
						<option value="VALKAS NOVADS">Valkas novads</option>
						<option value="VALMIERAS NOVADS">Valmieras novads</option>
						<option value="VARAKĻĀNU NOVADS">Varakļānu novads</option>
						<option value="VARKAVAS NOVADS">Vārkavas novads</option>
						<option value="VECPIEBALGAS NOVADS">Vecpiebalgas novads</option>
						<option value="VECUMNIEKU NOVADS">Vecumnieku novads</option>
						<option value="VENTSPILS NOVADS">Ventspils novads</option>
						<option value="VIESĪTES NOVADS">Viesītes novads</option>
						<option value="VIĻAKAS NOVADS">Viļakas novads</option>
						<option value="VIĻĀNU NOVADS">Viļānu novads</option>
						<option value="ZILUPES NOVADS">Zilupes novads</option>
					</select>
				</div>
  
				<div class="form-group">
					<label for="a_iela">Iela</label>
					<input type="text" name="a_iela" id="a_iela" class="form-control f3" placeholder="Iela" value="">
				</div>
				<div class="form-group has-feedback">
					<label for="a_maja">Mājas Nr./nosaukums</label>
					<input type="text" name="a_maja" id="a_maja" class="form-control f4" data-error="Šis ir obligāti aizpildāms lauks." placeholder="Iela" value="" maxlength="30" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group">
					<label for="a_dzivoklis">Dzīvokļa Nr</label>
					<input type="text" name="a_dzivoklis" id="a_dzivoklis" placeholder="Dzīvokļa Nr" class="form-control f5" placeholder="" value="" maxlength="4">
				</div>
				<div class="form-group has-feedback">
					<label for="a_indekss">Pasta indekss</label>
					<div class="input-group">
						<span class="input-group-addon">LV -</span>
						<input type="text" name="a_indekss" id="a_indekss" class="form-control f6" placeholder="Pasta indekss" data-error="Šis ir obligāti aizpildāms lauks." value="" maxlength="4" required>
					</div>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
     
				<p class="lead">Pieraksta vai deklarētā adrese</p>
   				<label for="sakrit-ar-dzivesvietu" class="checkbox-inline"><input type="checkbox" id="sakrit-ar-dzivesvietu" class="checkbox" name="a2_check">Sakrīt ar dzīvesvietu?</label>
				<div class="form-group has-feedback">
					<label for="a2_pagpil">Pagasts/pilsēta</label>
					<input type="text" name="a2_pagpil" id="a2_pagpil" class="form-control d1"
						data-error="Šis ir obligāti aizpildāms lauks." placeholder="Pagasts/pilsēta"
						value="" maxlength="20" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group">
					<label for="a2_rajons">Novads</label>
					<select class="form-control obligate d2" name="a2_rajons" id="a2_rajons" required>
						<option value=""></option>
						<option value="-------">-------</option>
						<option value="AGLONAS NOVADS">Aglonas novads</option>
						<option value="AIZKRAUKLES NOVADS">Aizkraukles novads</option>
						<option value="AIZPUTES NOVADS">Aizputes novads</option>
						<option value="AKNĪSTES NOVADS">Aknīstes novads</option>
						<option value="ALOJAS NOVADS">Alojas novads</option>
						<option value="ALSUNGAS NOVADS">Alsungas novads</option>
						<option value="ALŪKSNES NOVADS">Alūksnes novads</option>
						<option value="AMATAS NOVADS">Amatas novads</option>
						<option value="APES NOVADS">Apes novads</option>
						<option value="AUCES NOVADS">Auces novads</option>
						<option value="ĀDAŽU NOVADS">Ādažu novads</option>
						<option value="BABĪTES NOVADS">Babītes novads</option>
						<option value="BALDONES NOVADS">Baldones novads</option>
						<option value="BALTINAVAS NOVADS">Baltinavas novads</option>
						<option value="BALVU NOVADS">Balvu novads</option>
						<option value="BAUSKAS NOVADS">Bauskas novads</option>
						<option value="BEVERĪNAS NOVADS">Beverīnas novads</option>
						<option value="BROCĒNU NOVADS">Brocēnu novads</option>
						<option value="BURTNIEKU NOVADS">Burtnieku novads</option>
						<option value="CARNIKAVAS NOVADS">Carnikavas novads</option>
						<option value="CESVAINES NOVADS">Cesvaines novads</option>
						<option value="CĒSU NOVADS">Cēsu novads</option>
						<option value="CIBLAS NOVADS">Ciblas novads</option>
						<option value="DAGDAS NOVADS">Dagdas novads</option>
						<option value="DAUGAVPILS NOVADS">Daugavpils novads</option>
						<option value="DOBELES NOVADS">Dobeles novads</option>
						<option value="DUNDAGAS NOVADS">Dundagas novads</option>
						<option value="DURBES NOVADS">Durbes novads</option>
						<option value="ENGURES NOVADS">Engures novads</option>
						<option value="ĒRGĻU NOVADS">Ērgļu novads</option>
						<option value="GARKALNES NOVADS">Garkalnes novads</option>
						<option value="GROBIŅAS NOVADS">Grobiņas novads</option>
						<option value="GULBENES NOVADS">Gulbenes novads</option>
						<option value="IECAVAS NOVADS">Iecavas novads</option>
						<option value="IKŠĶILES NOVADS">Ikšķiles novads</option>
						<option value="ILŪKSTES NOVADS">Ilūkstes novads</option>
						<option value="INČUKALNA NOVADS">Inčukalna novads</option>
						<option value="JAUNJELGAVAS NOVADS">Jaunjelgavas novads</option>
						<option value="JAUNPIEBALGAS NOVADS">Jaunpiebalgas novads</option>
						<option value="JAUNPILS NOVADS">Jaunpils novads</option>
						<option value="JELGAVAS NOVADS">Jelgavas novads</option>
						<option value="JĒKABPILS NOVADS">Jēkabpils novads</option>
						<option value="KANDAVAS NOVADS">Kandavas novads</option>
						<option value="KĀRSAVAS NOVADS">Kārsavas novads</option>
						<option value="KOCĒNU NOVADS">Kocēnu novads</option>
						<option value="KOKNESES NOVADS">Kokneses novads</option>
						<option value="KRĀSLAVAS NOVADS">Krāslavas novads</option>
						<option value="KRIMULDAS NOVADS">Krimuldas novads</option>
						<option value="KRUSTPILS NOVADS">Krustpils novads</option>
						<option value="KULDĪGAS NOVADS">Kuldīgas novads</option>
						<option value="ĶEGUMA NOVADS">Ķeguma novads</option>
						<option value="ĶEKAVAS NOVADS">Ķekavas novads</option>
						<option value="LIELVĀRDES NOVADS">Lielvārdes novads</option>
						<option value="LIMBAŽU NOVADS">Limbažu novads</option>
						<option value="LĪGATNES NOVADS">Līgatnes novads</option>
						<option value="LĪVĀNU NOVADS">Līvānu novads</option>
						<option value="LUBĀNAS NOVADS">Lubānas novads</option>
						<option value="LUDZAS NOVADS">Ludzas novads</option>
						<option value="MADONAS NOVADS">Madonas novads</option>
						<option value="MAZSALACAS NOVADS">Mazsalacas novads</option>
						<option value="MĀLPILS NOVADS">Mālpils novads</option>
						<option value="MĀRUPES NOVADS">Mārupes novads</option>
						<option value="MĒRSRAGA NOVADS">Mērsraga novads</option>
						<option value="NAUKŠĒNU NOVADS">Naukšēnu novads</option>
						<option value="NERETAS NOVADS">Neretas novads</option>
						<option value="NĪCAS NOVADS">Nīcas novads</option>
						<option value="OGRES NOVADS">Ogres novads</option>
						<option value="OLAINES NOVADS">Olaines novads</option>
						<option value="OZOLNIEKU NOVADS">Ozolnieku novads</option>
						<option value="PĀRGAUJAS NOVADS">Pārgaujas novads</option>
						<option value="PĀVILOSTAS NOVADS">Pāvilostas novads</option>
						<option value="PĻAVIŅU NOVADS">Pļaviņu novJads</option>
						<option value="PREIĻU NOVADS">Preiļu novads</option>
						<option value="PRIEKULES NOVADS">Priekules novads</option>
						<option value="PRIEKUĻU NOVADS">Priekuļu novads</option>
						<option value="RAUNAS NOVADS">Raunas novads</option>
						<option value="RĒZEKNES NOVADS">Rēzeknes novads</option>
						<option value="RIEBIŅU NOVADS">Riebiņu novads</option>
						<option value="RĪGAS NOVADS">Rīgas novads</option>
						<option value="ROJAS NOVADS">Rojas novads</option>
						<option value="ROPAŽU NOVADS">Ropažu novads</option>
						<option value="RUCAVAS NOVADS">Rucavas novads</option>
						<option value="RUGĀJU NOVADS">Rugāju novads</option>
						<option value="RUNDĀLES NOVADS">Rundāles novads</option>
						<option value="RŪJIENAS NOVADS">Rūjienas novads</option>
						<option value="SALACGRĪVAS NOVADS">Salacgrīvas novads</option>
						<option value="SALAS NOVADS">Salas novads</option>
						<option value="SALASPILS NOVADS">Salaspils novads</option>
						<option value="SALDUS NOVADS">Saldus novads</option>
						<option value="SAULKRASTU NOVADS">Saulkrastu novads</option>
						<option value="SĒJAS NOVADS">Sējas novads</option>
						<option value="SIGULDAS NOVADS">Siguldas novads</option>
						<option value="SKRĪVERU NOVADS">Skrīveru novads</option>
						<option value="SKRUNDAS NOVADS">Skrundas novads</option>
						<option value="SMILTENES NOVADS">Smiltenes novads</option>
						<option value="STOPIŅU NOVADS">Stopiņu novads</option>
						<option value="STRENČU NOVADS">Strenču novads</option>
						<option value="TALSU NOVADS">Talsu novads</option>
						<option value="TĒRVETES NOVADS">Tērvetes novads</option>
						<option value="TUKUMA NOVADS">Tukuma novads</option>
						<option value="VAIŅODES NOVADS">Vaiņodes novads</option>
						<option value="VALKAS NOVADS">Valkas novads</option>
						<option value="VALMIERAS NOVADS">Valmieras novads</option>
						<option value="VARAKĻĀNU NOVADS">Varakļānu novads</option>
						<option value="VARKAVAS NOVADS">Vārkavas novads</option>
						<option value="VECPIEBALGAS NOVADS">Vecpiebalgas novads</option>
						<option value="VECUMNIEKU NOVADS">Vecumnieku novads</option>
						<option value="VENTSPILS NOVADS">Ventspils novads</option>
						<option value="VIESĪTES NOVADS">Viesītes novads</option>
						<option value="VIĻAKAS NOVADS">Viļakas novads</option>
						<option value="VIĻĀNU NOVADS">Viļānu novads</option>
						<option value="ZILUPES NOVADS">Zilupes novads</option>
					</select>
				</div>
				<div class="form-group">
					<label for="a2_iela">Iela</label>
					<input type="text" name="a2_iela" id="a2_iela" class="form-control d3"
						placeholder="Iela" value="">
				</div>
				<div class="form-group has-feedback">
					<label for="a2_maja">Mājas Nr./nosaukums</label>
					<input type="text" name="a2_maja" id="a2_maja" class="form-control obligate d4"
						placeholder="Mājas Nr./nosaukums" data-error="Šis ir obligāti aizpildāms lauks."
						value="" maxlength="30" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group">
					<label for="a2_dzivoklis">Dzīvokļa Nr</label>
					<input type="text" name="a2_dzivoklis" id="a2_dzivoklis" placeholder="Dzīvokļa Nr"
						class="form-control obligate d5" placeholder="" value="" maxlength="4">
				</div>
				<div class="form-group has-feedback">
					<label for="a2_indekss">Pasta indekss</label>
					<div class="input-group">
						<span class="input-group-addon">LV -</span>
						<input type="text" name="a2_indekss" id="a2_indekss" class="form-control d6"
							placeholder="Pasta indekss" data-error="Šis ir obligāti aizpildāms lauks."
							value="" maxlength="4" mask="NNNN" required>
					</div>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>

				<p class="lead hide">Kontaktinformācija</p>
				<div class="form-group hide">
					<label for="a_maju_tel_1">Mājas tālruņa Nr.</label>
					<div class="input-group">
						<span class="input-group-addon">+371</span>
						<input type="text" name="a_maju_tel" id="a_maju_tel" class="form-control" placeholder="" value="" maxlength="20">
					</div>
				</div>
				<div class="form-group has-feedback hide">
					<label for="a_mob_tel">Mobilā tālruņa Nr.</label>
					<div class="input-group">
						<span class="input-group-addon">+371</span>
						<input type="text" name="a_mob_tel" id="a_mob_tel" class="form-control" data-error="Šis ir obligāti aizpildāms lauks." placeholder="" value="'.ltrim($this->getModel()->person->phone, "+371").'" maxlength="20" required>
					</div>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group hide">
					<label for="a_darba_tel">Darba tālruņa Nr.</label>
					<div class="input-group">
						<span class="input-group-addon">+371</span>
						<input type="text" name="a_darba_tel" id="a_darba_tel" class="form-control" placeholder="" value="" maxlength="20">
					</div>
				</div>
				<div class="form-group hide">
					<label for="Email">E-pasts</label>
					<input type="email" name="Email" id="Email" placeholder="E-pasts" class="form-control obligate" placeholder="" value="'.$this->getModel()->person->email.'" maxlength="20" data-error="Šī e-pasta adrese nav derīga">
					<div class="help-block with-errors"></div>
				</div>
	
				<p class="lead">Darbavieta</p>
				<div class="form-group has-feedback">
					<label for="a_darbavieta">Darba vieta</label>
					<input type="text" name="a_darbavieta" id="a_darbavieta" placeholder="Darba vieta"
						class="form-control" data-error="Šis ir obligāti aizpildāms lauks."
						placeholder="" value="'.($this->getModel()->extra ? $this->getModel()->extra->car_workplace : null).'" maxlength="50" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group has-feedback">
					<label for="a_amats">Amats</label>
					<input type="text" name="a_amats" id="a_amats" placeholder="Amats" class="form-control"
						data-error="Šis ir obligāti aizpildāms lauks." placeholder="" value="'.($this->getModel()->extra ? $this->getModel()->extra->car_position : null).'"
						maxlength="50" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group has-feedback">
					<label for="a_stazs">Stāžs m.</label>
					<input type="text" name="a_stazs" id="a_stazs" placeholder="Stāžs m."
						class="form-control" data-error="Šis ir obligāti aizpildāms lauks."
						placeholder="" value="'.($this->getModel()->extra ? $this->getModel()->extra->car_work_experience : null).'" maxlength="50" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group has-feedback hide">
					<label for="a_neto">Neto ienākumi</label>
					<input type="text" name="a_neto" id="a_neto" placeholder="Neto ienakumi"
						class="form-control" data-error="Šis ir obligāti aizpildāms lauks."
						placeholder="" value="'.$this->getModel()->person->income.'" maxlength="7" mask="money" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>

				<p class="lead aiznemejs-switch">Kontaktpersona aizņēmēja prombūtnē<small>(izņemot dzīvesbiedru)</small></p>
				<div class="form-group has-feedback">
					<label for="k_vards">Vārds</label>
					<input type="text" name="k_vards" placeholder="Vārds" id="k_vards" class="form-control" data-minlength="3" data-error="Šis ir obligāti aizpildāms lauks." placeholder="" tabindex="" value="'.$rnn.'" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group has-feedback">
					<label for="k_uzvards">Uzvārds</label>
					<input type="text" name="k_uzvards" placeholder="Uzvārds" id="k_uzvards" class="form-control" data-error="Šis ir obligāti aizpildāms lauks." data-minlength="3" placeholder="" tabindex="" value="'.$rns.'" required>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>
				<div class="form-group has-feedback">
					<label for="k_tel">Mobilā tālruņa Nr.</label>
					<div class="input-group">
						<span class="input-group-addon">+371</span>
						<input type="text" name="k_tel" id="k_tel" class="form-control" data-error="Šis ir obligāti aizpildāms lauks." placeholder="" value="'.$rnr.'" required>
					</div>
					<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
					<span class="help-block with-errors"></span>
				</div>

				<!--<p class="lead aiznemejs-switch">Klients piekrīt VSAA pārbaudei</p>
				<p><em>Gadījumos, ja klients pieteikumu parakstīs uz vietas un piekrīt VSAA pārbaudei</em></p>
				<div class="checkbox">
					<input type="hidden" name="VSAACheck" value="0" />
					<label><input type="checkbox" name="VSAACheck" id="VSAACheck" value="1"> atļauju Valsts sociālās apdrošināšanas aģentūrai sniegt šādus manus personas datus par pēdējiem sešiem mēnešiem pirms datu pieprasīšanas dienas <small><em>norādiet ienākuma avotu</em></small>:</label>
				</div>
				<div class="checkbox">
					<input type="hidden" name="VSAASalaryCheck" value="0" />
					<label><input type="checkbox" name="VSAASalaryCheck" id="VSAASalaryCheck" value="1"> Informācija par sociālās apdrošināšanas iemaksām un apdrošināšanas periodiem;</label>
				</div>
				<div class="checkbox">
					<input type="hidden" name="VSAAPensionCheck" value="0" />
					<label><input type="checkbox" name="VSAAPensionCheck" id="VSAAPensionCheck" value="1"> Informācija par izmaksai nosūtīto pensiju/pabalstu/atlīdzību;</label>
				</div>-->
				<div class="form-group">
					<label for="">Papildus komentārs</label>
					<textarea name="Comment" id="Comment" class="form-control comment" maxlength="180"
						rows="2"></textarea>
				</div>
				<!--<div class="form-group">
					<input type="checkbox" name="t_and_c" id="t_and_c" class="" value="" class="checkbox-inline"
						data-error="Šim laukam jābūt atzīmētam" required>
					<p>Ar šo apstiprinu, ka vēlos saņemt aizdevumu uz SIA “Aizdevums.lv“ piedāvātajiem noteikumiem.
						Apliecinu, ka visa šajā pieteikumā sniegtā informācija ir patiesa un pilnīga. Parakstot šo
						pieteikumu, izsaku savu piekrišanu visu pieteikumā norādīto personu datu apstrādei.
						Apliecinu, ka esmu saņēmis pieteikumā norādīto personu piekrišanu viņu personas datu
						apstrādei un saglabāšanai, tajā skaitā elektroniski. Neiebilstu, ka manis sniegtā
						informācija tiek pārbaudīta caur trešajām personām, kā arī var tikt pieprasītas ziņas no
						Latvijas bankas Kredītu reģistra, informācija var tikt saglabāta, tajā skaitā elektroniski.
						Esmu informēts un piekrītu, ka datus apstrādā SIA “Aizdevums.lv”, vien.reģ.Nr.40003468776,
						juridiskā adrese Rīga, Cēsu iela 31 k-3 vai citas SIA “Aizdevums.lv“ pilnvarotās personas,
						atbilstoši Fizisko personu datu aizsardzības likuma prasībām (SIA “Aizdevums.lv“ datu
						apstrāde ir reģistrēta Datu Valsts inspekcijā. Apstrādes reģistrācijas Nr. 000813).
						Pieprasītās info nesniegšana tiek vērtēta kā attiecīgo ziņu neesamība. Par nepatiesu ziņu
						sniegšanu personai draud kriminālatbildība.
					</p>
					<div class="help-block with-errors"></div>
				</div>-->
				<input type="submit" id="aizdevums-post" value="Nosūtīt pieteikumu" class="btn btn-primary btn-block btn-lg" tabindex="">
			</form></div>';
			$html .= "<div id='aizdevums-api-info'></div>";
		}
		return $html;
	}
	

	private function _setContractId($id) {
		$this->_log("SET_CONTRACT_ID - CONTRACT_ID - ".$id." - MODEL_ID - ".$this->getModel()->id);
	
		\Yii::$app->db->createCommand("INSERT INTO `aizdevums_api` (`id`, `loan_id`, `contract_id`) VALUES (NULL, {$this->getModel()->id}, {$id});")->execute();
	}
	
	private function _deleteContract() {
		$id = $this->_getContractId();
		
		if (!$id)
			return;
		
		$this->_log("DELETE_CONTRACT_ID - CONTRACT_ID - ".$id." - MODEL_ID - ".$this->getModel()->id);
	
		\Yii::$app->db->createCommand("DELETE FROM `aizdevums_api` WHERE contract_id='".$id."';")->execute();
	}
	
	private function _getContractId() {
		if ($data = \Yii::$app->db->createCommand("SELECT *  FROM `aizdevums_api` WHERE `loan_id` = ".$this->getModel()->id)->queryOne()) {
			return $data["contract_id"];
		}
	
		return null;
	}
	
	private function _setContractNr($id) {
		$this->_log("SET_CONTRACT_NR - CONTRACT_NR - ".$id." - MODEL_ID - ".$this->getModel()->id);
	
		\Yii::$app->db->createCommand("UPDATE `aizdevums_api` SET `contract_nr` =  '".$id."' WHERE  `aizdevums_api`.`contract_id` ={$this->_getContractId()};")->execute();
	}
	
	private function _getContractNr() {
		if ($data = \Yii::$app->db->createCommand("SELECT *  FROM `aizdevums_api` WHERE `loan_id` = ".$this->getModel()->id)->queryOne()) {
			return $data["contract_nr"];
		}
	
		return null;
	}
	
	private function _log($msg) {
		$fd = fopen(\Yii::$app->params["aizdevumsapi_log_path"], "a+");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $str . "\n");
		fclose($fd);
	}
}

//Pārveidotā Soapservice klase, lai varētu autorizēties ar NTLM autorizēšanās metodi
class Ntlmservice extends \SoapClient
{
	protected $options;
		
	public function __construct($url, $options = [])
	{
		$this->options = $options;
		$username = $this->options['login'];
		$password = $this->options['password'];
		$url = $this->options['url'];
		$path = $this->options['local_path'];

		//Skatamies vai wsdl ir lokāls vai caur linku
		if (!file_exists($url)) {
			$handle = curl_init();

			curl_setopt($handle, CURLOPT_URL, $url);
			curl_setopt($handle, CURLOPT_HEADER, false);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($handle, CURLOPT_TIMEOUT, 30);
			
			curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
			curl_setopt($handle, CURLOPT_USERPWD, "{$username}:{$password}");

			$response = curl_exec($handle);
			curl_close($handle);

			
			file_put_contents($path, $response);
			$url = $path;
		}

		parent::__construct($url, $options);
	}


	public function __doRequest($request, $location, $action, $version, $one_way = false)
	{
		//Taisam replace ns1, savādāk ir nepareizi elementa nosaukumi, ja priekšā ir ns1
		$createwebapplication = 'urn:microsoft-dynamics-schemas/codeunit/Web_Applications:CREATEWEBAPPLICATION';

		if ($action == $createwebapplication) {
			$request = str_replace('<ns1:', '<', $request);
			$request = str_replace('</ns1:', '</', $request);
		}


		$this->__last_request = $request;

		$handle = curl_init($location);

		$credentials = $this->options['login'] . ':' . $this->options['password'];
		$headers = [
				'Method: POST',
				'User-Agent: PHP-SOAP-CURL',
				'Content-Type: text/xml; charset=utf-8',
				'SOAPAction: "' . $action . '"'
		];

		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $request);
		curl_setopt($handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Autorizācija
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		curl_setopt($handle, CURLOPT_USERPWD, $credentials);

		$response = curl_exec($handle);

		return $response;
	}
}








function CreateWebApplication($service, $webApplicationXML) {
	try {
		$response = $service->CREATEWEBAPPLICATION($webApplicationXML);
	} catch (\Exception $e) {
		echo json_encode(["html" => "Kļūda: Neizdevās izveidot jaunu pieteikumu: ".$e->getMessage()."", "valid" => false]);
		
	}
	
	if (isset($response->return_value)) {
		$webAppNo = $response->return_value;
		
		echo json_encode(["html" => "Izveidotais pieteikuma numurs ir $webAppNo", "valid" => true, "nr" => $webAppNo]);
	}
}
function arrayToObject($d) {
	if (is_array($d)) {
		/*
		 * Return array converted to object
		 * Using __FUNCTION__ (Magic constant)
		 * for recursive call
		 */
		return (object) array_map(__FUNCTION__, $d);
	} else {
		// Return object
		return $d;
	}
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		 * Return array converted to object
		 * Using __FUNCTION__ (Magic constant)
		 * for recursive call
		 */
		return array_map(__FUNCTION__, $d);
	} else {
		// Return array
		return $d;
	}
}

function CheckStatus($service, $number) {
	//Atbilde par pieteikuma statusu
	$params = array(
			'webAppNo' => $number,
			'responseXML' => '',
	);
	$response = $service->CHECKSTATUS($params);

	//var_dump($response);
	$array = objectToArray($response);
	$html = "<hr>";
	$html .= "<table class='table'";
	$html .= '<tr><th>Status</th><th>ContractNo</th><th>Comment</th><th>MonthPayment</th><th>InterestRate</th><th>FirstPayment</th><th>ContractFee</th></tr>';
	foreach ($array as $item) {
		$html .= '<tr>';
		$html .= '<td>' . $item['Status'] . '</td>';
		$html .= '<td>' . $item['ContractNoORStatus'] . '</td>';
		$html .= '<td>' . $item['Comment'] . '</td>';
		$html .= '<td>' . $item['MonthPayment'] . '</td>';
		$html .= '<td>' . $item['InterestRate'] . '</td>';
		$html .= '<td>' . $item['FirstPayment'] . '</td>';
		$html .= '<td>' . $item['ContractFee'] . '</td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
	
	$refresh = false;
	if ((strpos($item['ContractNoORStatus'], \Yii::$app->params["aizdevumsapi_code"]) !== false)) {
		$refresh = true;
	}
	
	echo json_encode(["html" => $html, "status" => $item['ContractNoORStatus'], "refresh" => $refresh]);
	
}

function UploadContract($service, $secNo) {
	//Līguma dokumentu ielādēšana/drukāšana no Sadarbības partnera puses.
	$params = array(
			'contractNoAndSocSecNo' => $secNo,
			'pDFFile' => '',
			'comment' => '',
	);

	$response = $service->UPLOADCONTRACT($params);
	$pdf = $response->pDFFile;

	if (!empty($pdf)) {
		$html = "
		<form method='post' action='' target='_blank'>
		<input type='hidden' name='_csrf' value='".\Yii::$app->request->csrfToken."'>
		<input type='hidden' name='pdf' value='$pdf'/>
		<input type='hidden' name='task' value='aizdevums-file'/>
		<input type='submit' class='btn btn-success'  value='Atvērt failu'/>
		</form>
		";
	} else {
		//var_dump($response);
		$html = $comment = $response->comment;
	}
	echo json_encode(["html" => $html]);
}

function ContractSigned($service, $contractNo) {
	//Līguma parakstīšana.
	if (strlen($contractNo) > 30) {
		$html = 'Nedrīkst ievadīt vairāk par 30 simboliem!';
	} else {
		$params = array(
				'contractNo' => $contractNo,
				'responseXML' => '',
		);

		$response = $service->CONTRACTSIGNED($params);
		//var_dump($response);

		$array = objectToArray($response);
		$html = "<hr>";
		$html .= "<table class='table'";
		$html .= '<tr><th>Status</th><th>Comment</th></tr>';
		foreach ($array as $item) {
			$html .= '<tr>';
			$html .= '<td>' . $item['Status'] . '</td>';
			$html .= '<td>' . $item['Comment'] . '</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
	}
	
	echo json_encode(["html" => $html]);
}

function CancelApplication($service, $webAppNo) {
	$refresh = false;
	//Pieteikuma/līguma atcelšana.
	$params = array(
			'webAppNo' => $webAppNo,
			'responseXML' => '',
	);

	$response = $service->CANCELAPPLICATION($params);
	//var_dump($response);

	$array = objectToArray($response);

	$html =  '<hr>';
	$html .=  "<table class='table'";
	$html .=  '<tr><th>Status</th><th>Comment</th></tr>';
	foreach ($array as $item) {
		$html .=  '<tr>';
		$html .=  '<td>' . $item['Status'] . '</td>';
		$html .=  '<td>' . $item['Comment'] . '</td>';
		$html .=  '</tr>';
	}
	$html .=  '</table>';
	
	if (strtolower($item['Status']) == "yes")
		$refresh = true;
	
	echo json_encode(["html" => $html, "status" => $item['Status'], "refresh" => $refresh]);
}

//Funkcija paredzēta, lai pārbaudītu vai vērtība eksistē, kura nāk no POST vai GET
function checkIssetData($value) {
	if (isset($value)) {
		return $value;
	} else {
		return '';
	}
}
		
class AizdevumsAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $css = [
	];

	public $js = [
			'js/aizdevums.js?v=2',
	];

	public $depends = [
			'yii\web\YiiAsset',
			'yii\bootstrap\BootstrapAsset',
	];
}