<?php 
namespace app\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Loan;
use app\models\LoanProgerss;

class LoanTFBankBehavior extends Behavior
{
	public function send($event)
	{
		if ($event->product == 3) {
			$this->_log(" product_id = 3 so skip ".$event->id);
			return;
		}
		
		if ($event->person->email == "yam.aka.as@gmail.com ".$event->id) {
			$this->_log(" fake so skip");
			return;
		}
		
		if ($this->_getContractId($event)) {
			$this->_log(" has contract ID so skip ".$event->id);
			return;
		}
		
		sleep(1);
		
		$months_values = array(12,24,36,46,60);
		$api_term = $this->closest($months_values, $event->term);
		
		$client = new \SoapClient(\Yii::$app->params["tfbank_url"], array('soap_version' => SOAP_1_1));
		$propParams = [
				'PropertyOwner' => false
		];
		$params = array(
				//'ActivityCode'=>'ACTIVITY_CODE_PHP', //done
				'Affiliate' => 'OneFinance-2002-10001', //done
				'Amount' => $event->amount, //done
				'Applicant' => array(
						'Accommodation' => array(
								'AdultsInFamily' => null, //done
								'ChildrenInFamily' => $event->person->dependants, //done
								'Since' => null, //done
								'Type' => "Other"//done
						),
						'Address' => array(
								'Street' => "NAV ADRESES", //done
								'City' => "NAV ADRESES", //done
								'Zip' => "LV-9999", //done
						),
						'ContactAddress' => array(
								'Street' => "NAV ADRESES", //done
								'City' => "NAV ADRESES", //done
								'Zip' => "LV-9999", //done
						),
						'ContactAddressDiffers' => true,
						'Contacts' => array(
								'CellPhone' => $event->person->phone,
								'Email' => $event->person->email,
						),
						'Employment' =>array(
								'EmployedSince' => $this->getWorkExp($event),
								'EmployerName' => ($event->extra ? $event->extra->car_workplace : null),
								'EmployerPhone' => null,
								'Income' => 12*$event->person->income,
								'OccupationType' => 'Permanent'
						),
						'Loans' => array(
								'OtherMonthlyCost' => $event->person->outcome,
								//'OtherTotal' =>
						),
						$propParams,
						'PersonalInfo' => array(
								'FirstName' => $event->person->name, //done
								'Gender' => $this->getGender($event), //done
								'LastName' => $event->person->surname, //done
								'MaritalStatus' => $this->getMaritalStatus($event), //done
								'Ssn' => $event->person->personal_code //done
						),
				),
				'BankInfo' => array(
						'Iban' => ($event->extra ? "LV66BANK0000000000000" : "LV66BANK0000000000000") //done
				),
				'Channel' => 'ExternalPartner', //done
				'ClientIp' => $event->ip_ountry, //done
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
		
		$content = "";
		
		try {
			$result = $client->RegisterApplication(array('request'=>$params));
			
			$this->_log("Result - ".json_encode($result->RegisterApplicationResult));
			
			if ($result->RegisterApplicationResult->Errors) {
				$valid = 0;
				$content = $result->RegisterApplicationResult->Errors->Error->Message;
			} else {
				if ($result->RegisterApplicationResult->ApplicationNumber) {
					$valid = 1;
					$this->_setContractId($result->RegisterApplicationResult->ApplicationNumber, $result->RegisterApplicationResult->Decision, $event);
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
					
					$mod = $event;
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
		
		$this->_log($content);
	}
	
	private function _setContractId($id, $status, $event) {
		$this->_log("SET_CONTRACT_ID - CONTRACT_ID - ".$id." - ".$status." - MODEL_ID - ".$event->id);
		
		\Yii::$app->db->createCommand("INSERT INTO `tfbank_api` (`id`, `loan_id`, `contract_id`, `status`) VALUES (NULL, {$event->id}, {$id}, '{$status}');")->execute();
		
		$loanProg = new LoanProgerss();
		$loanProg->loan_id = $event->id;
		$loanProg->provider_id = 11;
		$loanProg->status = 1;
		$loanProg->save();
	}
	
	private function _getContractId($event) {
		if ($data = \Yii::$app->db->createCommand("SELECT *  FROM `tfbank_api` WHERE `loan_id` = ".$event->id)->queryOne()) {
			return $data;
		}
		
		return null;
	}
	private function closest($array, $number)
	{
	    
	    sort($array);
	    foreach ($array as $a) {
	        if ($a >= $number) return $a;
	    }
	    
	    return end($array); // or return NULL;
	}
	
	private function getMaritalStatus($event) {
		if ($event->person->family_status == null) {
			return "Unknown";
		}
		if ($event->person->family_status == 0) {
			return "Single";
		}
		if ($event->person->family_status == 1) {
			return "Cohabitee";
		}
		if ($event->person->family_status == 2) {
			return "Married";
		}
		if ($event->person->family_status == 3) {
			return "Divorced";
		}
		if ($event->person->family_status == 4) {
			return "Single";
		}
		
		return "Unknown";
	}
	
	private function getGender($event) {
		if ($event->person->gender == "f") {
			return "Female";
		}
		if ($event->person->gender == "m") {
			return "Male";
		}
		
		return "Unknown";
	}
	
	private function getWorkExp($event) {
		if ($event->extra) {
			if ($event->extra->car_workplace) {
				$time = @strtotime("-".$event->extra->car_workplace." months");
				if ($time) {
					return date("c", $time);
				}
			}
		}
		
		return null;
	}
	
	private function getZip($address) {
		if ($address == "LV-9999") {
			return "LV-9999";
		}
		if (!$address) {
			return "LV-9999";
		}
		return substr(strrchr($address, 'LV-'), 0, 7);
	}
	
	private function getCity($address) {
		if ($address == "NAV ADRESES") {
			return "NAV ADRESES";
		}
		if (!$address) {
			return "NAV ADRESES";
		}
		$cities = [
				"Rīga" => "Rīga",
				"Daugavpils" => "Daugavpils",
				"Jēkabpils" => "Jēkabpils",
				"Jelgava" => "Jelgava",
				"Jūrmala" => "Jūrmala",
				"Liepāja" => "Liepāja",
				"Rēzekne" => "Rēzekne",
				"Valmiera" => "Valmiera",
				"Ventspils" => "Ventspils",
				"Aglonas" => "Aglonas novads",
				"Aizkraukles" => "Aizkraukles novads",
				"Aizputes" => "Aizputes novads",
				"Aknīstes" => "Aknīstes novads",
				"Alojas" => "Alojas novads",
				"Alsungas" => "Alsungas novads",
				"Alūksnes" => "Alūksnes novads",
				"Amatas" => "Amatas novads",
				"Apes" => "Apes novads",
				"Auces" => "Auces novads",
				"Ādažu" => "Ādažu novads",
				"Babītes" => "Babītes novads",
				"Baldones" => "Baldones novads",
				"Baltinavas" => "Baltinavas novads",
				"Balvu" => "Balvu novads",
				"Bauskas" => "Bauskas novads",
				"Beverīnas" => "Beverīnas novads",
				"Brocēnu" => "Brocēnu novads",
				"Burtnieku" => "Burtnieku novads",
				"Carnikavas" => "Carnikavas novads",
				"Cēsu" => "Cēsu novads",
				"Cesvaines" => "Cesvaines novads",
				"Ciblas" => "Ciblas novads",
				"Dagdas" => "Dagdas novads",
				"Daugavpils" => "Daugavpils novads",
				"Dobeles" => "Dobeles novads",
				"Dundagas" => "Dundagas novads",
				"Durbes" => "Durbes novads",
				"Engures" => "Engures novads",
				"Ērgļu" => "Ērgļu novads",
				"Garkalnes" => "Garkalnes novads",
				"Grobiņas" => "Grobiņas novads",
				"Gulbenes" => "Gulbenes novads",
				"Iecavas" => "Iecavas novads",
				"Ikšķiles" => "Ikšķiles novads",
				"Inčukalna" => "Inčukalna novads",
				"Ilūkstes" => "Ilūkstes novads",
				"Jaunjelgavas" => "Jaunjelgavas novads",
				"Jaunpiebalgas" => "Jaunpiebalgas novads",
				"Jaunpils" => "Jaunpils novads",
				"Jēkabpils" => "Jēkabpils novads",
				"Jelgavas" => "Jelgavas novads",
				"Kandavas" => "Kandavas novads",
				"Kārsavas" => "Kārsavas novads",
				"Kokneses" => "Kokneses novads",
				"Krāslavas" => "Krāslavas novads",
				"Krimuldas" => "Krimuldas novads",
				"Krustpils" => "Krustpils novads",
				"Kuldīgas" => "Kuldīgas novads",
				"Ķeguma" => "Ķeguma novads",
				"Ķekavas" => "Ķekavas novads",
				"Lielvārdes" => "Lielvārdes novads",
				"Līgatnes" => "Līgatnes novads",
				"Limbažu" => "Limbažu novads",
				"Līvānu" => "Līvānu novads",
				"Lubānas" => "Lubānas novads",
				"Ludzas" => "Ludzas novads",
				"Madonas" => "Madonas novads",
				"Mālpils" => "Mālpils novads",
				"Mārupes" => "Mārupes novads",
				"Mazsalacas" => "Mazsalacas novads",
				"Naukšēnu" => "Naukšēnu novads",
				"Neretas" => "Neretas novads",
				"Nīcas" => "Nīcas novads",
				"Ogres" => "Ogres novads",
				"Olaine" => "Olaines novads",
				"Ozolnieku" => "Ozolnieku novads",
				"Pārgaujas" => "Pārgaujas novads",
				"Pāvilostas" => "Pāvilostas novads",
				"Pļaviņu" => "Pļaviņu novads",
				"Preiļu" => "Preiļu novads",
				"Priekules" => "Priekules novads",
				"Priekuļu" => "Priekuļu novads",
				"Raunas" => "Raunas novads",
				"Rēzeknes" => "Rēzeknes novads",
				"Riebiņu" => "Riebiņu novads",
				"Rojas" => "Rojas novads",
				"Ropažu" => "Ropažu novads",
				"Rucavas" => "Rucavas novads",
				"Rugāju" => "Rugāju novads",
				"Rundāles" => "Rundāles novads",
				"Rūjienas" => "Rūjienas novads",
				"Salacgrīvas" => "Salacgrīvas novads",
				"Salas" => "Salas novads",
				"Salaspils" => "Salaspils novads",
				"Saldus" => "Saldus novads",
				"Saulkrastu" => "Saulkrastu novads",
				"Sējas" => "Sējas novads",
				"Siguldas" => "Siguldas novads",
				"Skrīveru" => "Skrīveru novads",
				"Skrundas" => "Skrundas novads",
				"Smiltenes" => "Smiltenes novads",
				"Stopiņu" => "Stopiņu novads",
				"Strenču" => "Strenču novads",
				"Talsu" => "Talsu novads",
				"Tērvetes" => "Tērvetes novads",
				"Tukuma" => "Tukuma novads",
				"Vaiņodes" => "Vaiņodes novads",
				"Valkas" => "Valkas novads",
				"Valmieras" => "Valmieras novads",
				"Varakļānu" => "Varakļānu novads",
				"Vārkavas" => "Vārkavas novads",
				"Vecpiebalgas" => "Vecpiebalgas novads",
				"Vecumnieku" => "Vecumnieku novads",
				"Ventspils" => "Ventspils novads",
				"Viesītes" => "Viesītes novads",
				"Viļakas" => "Viļakas novads",
				"Viļānu" => "Viļānu novads",
				"Zilupes" => "Zilupes novads",
		];
		foreach ($cities as $city => $cityValue) {
			if (strpos(mb_strtolower($address), strtolower(substr($city, 0, -1))) !== false) {
				return $cityValue;
			}
		}
		
		return $address;
	}
	
	private function getAddress($address) {
		if ($address == "NAV ADRESES") {
			return "NAV ADRESES";
		}
		if (!$address) {
			return "NAV ADRESES";
		}
		$c = str_replace($this->getZip($address), "", $address);
		$c = str_replace($this->getCity($address), "", $c);
		
		return trim($c);
	}
	
	public function log($msg) {
		$this->_log($msg);
	}
	
    private function _log($msg) {
		$fd = fopen(\Yii::$app->params["tfbankapi_log_path"], "a+");
    	$str = "[" . date("Y/m/d h:i:s", time()) . "] AUTO TFBANK SEND - " . $msg;
    	fwrite($fd, $str . "\n");
    	fclose($fd);
    }
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
