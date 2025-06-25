<?php

require (__DIR__ . "/init.php");
require (__DIR__ . "/../../cronpost/CronPost.php");
require (__DIR__ . "/../../cronpost/CronPost1Lizings.php");
require (__DIR__ . "/../../cronpost/CronPost1Aizdevums.php");
require (__DIR__ . "/../../cronpost/CronPostOnefinance.php");


use app\models\Loan;
use app\models\Person;
use app\models\LoanExtra;

class CronPostTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        
    }
    
    public function tearDown()
    {
        
    }
    
    public function testApi() {
    	$prevPersonCount = Person::find()->count();
    	$prevLoanCount = Loan::find()->count();
    	$prevLoanExtraCount = LoanExtra::find()->count();
    	 
    	$cronPost = new CronPost("http://localhost/app.onefingroup.com/web/api/post-data");
    	
    	$cronPost->post([
    			"Loan" => [
    					"amount"  => "1000",
    					"term" => "12",
    					"source" => "1aizdevums",
    			],
    			"Person" => [
    					"name" => "TestName",
    					"surname" => "TestSurname",
    					"personal_code" => time(),
    					"phone" => "1234567890",
    					"email" => "TestName@example.com",
    					"income" => "1000",
    					"outcome" => "1000",
    					"dependants" => "1",
    			],
    			"id" => time()
    	]);
    	
    	$this->assertEquals($prevPersonCount+1, Person::find()->count());
    	$this->assertEquals($prevLoanCount+1, Loan::find()->count());
    	$this->assertEquals($prevLoanExtraCount+1, LoanExtra::find()->count());
    }
    
    public function testApi1aizdevums() {
    	sleep(2);
    	 
    	$prevPersonCount = Person::find()->count();
    	$prevLoanCount = Loan::find()->count();
    	 
    	$servername = AIZDEVUMS_DB_HOST;
    	$serverdb = AIZDEVUMS_DB_NAME;
    	$username = AIZDEVUMS_DB_USER;
    	$password = AIZDEVUMS_DB_PASSWORD;
    	$conn = new PDO("mysql:host=$servername;dbname=$serverdb", $username, $password);
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	//$conn->exec("TRUNCATE wp_clients_forms;");
    	$conn->exec("INSERT INTO `wp_clients_forms` (`id`, `time`, `gender`, `name`, `surname`, `person_id`, `phone`, `email`, `income_month`, `loan_month`, `children`, `first_payment`, `amount`, `percent`, `type`, `months`, `api_status`, `api_status_dsc`, `referal`, `unique_id`, `client_ip`, `client_exists`, `is_send`) VALUES (NULL, '0000-00-00 00:00:00', '', 'Julija', 'Alaine', '".(time()+10)."', '1234567', 'ailaine@alaine.lv', '1000', '12', '3', '', '1000', '', 'aizdevums', '12', 'manual', 'Pagaidīt šāmējie neko nezin vēl?!@$&G*F', 'google', '', '123.123.123.123', '', '0');");
    	$conn->exec("INSERT INTO `wp_clients_forms` (`id`, `time`, `gender`, `name`, `surname`, `person_id`, `phone`, `email`, `income_month`, `loan_month`, `children`, `first_payment`, `amount`, `percent`, `type`, `months`, `api_status`, `api_status_dsc`, `referal`, `unique_id`, `client_ip`, `client_exists`, `is_send`) VALUES (NULL, '0000-00-00 00:00:00', '', 'Julija', 'Alaine2', '".(time()+20)."', '12345627', 'ail2aine@alaine.lv', '1000', '12', '3', '', '1000', '', 'aizdevums', '12', 'manual', 'Pagaidīt šāmējie neko nezin vēl?!@$&G*F', 'google', '', '123.123.123.123', '', '0');");
    	
    	$cronPost = new CronPost1Aizdevums("http://localhost/crm/web/api/post-data");
    	 
    	$cronPost->run();
    	 
    	$this->assertEquals($prevPersonCount+2, Person::find()->count());
    	$this->assertEquals($prevLoanCount+2, Loan::find()->count());
    	 
    	$cronPost->run();
    	
    	$this->assertEquals($prevPersonCount+2, Person::find()->count());
    	$this->assertEquals($prevLoanCount+2, Loan::find()->count());
    	 
    	$loan = Loan::find()->orderBy("id desc")->one();
    	$this->assertEquals(2, $loan->product);
    	$this->assertEquals("google", $loan->referral);
    	$this->assertEquals("123.123.123.123", $loan->ip_ountry);
    	$this->assertEquals("Pagaidīt šāmējie neko nezin vēl?!@$&G*F", $loan->description);
    	$this->assertEquals(3, $loan->person->credit_history);
    }
    
    public function testApi1lizings() {
    	sleep(2);
    	
    	$prevPersonCount = Person::find()->count();
    	$prevLoanCount = Loan::find()->count();
    	
    	$servername = LIZINGS_DB_HOST;
    	$serverdb = LIZINGS_DB_NAME;
    	$username = LIZINGS_DB_USER;
    	$password = LIZINGS_DB_PASSWORD;
    	$conn = new PDO("mysql:host=$servername;dbname=$serverdb", $username, $password);
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	//$conn->exec("TRUNCATE wp_clients_forms;");
    	$conn->exec("INSERT INTO `wp_clients_forms` (`id`, `time`, `gender`, `name`, `surname`, `person_id`, `phone`, `email`, `income_month`, `loan_month`, `children`, `first_payment`, `amount`, `percent`, `type`, `months`, `api_status`, `api_status_dsc`, `referal`, `unique_id`, `client_ip`, `client_exists`, `is_send`) VALUES (NULL, '0000-00-00 00:00:00', '', 'Julija', 'Alaine', '".(time()+10)."', '1234567', 'ailaine@alaine.lv', '1000', '12', '3', '', '1000', '', '', '12', 'manual', 'Pagaidīt šāmējie neko nezin vēl?!@$&G*F', '', '', '', '', '0');");
    	$conn->exec("INSERT INTO `wp_clients_forms` (`id`, `time`, `gender`, `name`, `surname`, `person_id`, `phone`, `email`, `income_month`, `loan_month`, `children`, `first_payment`, `amount`, `percent`, `type`, `months`, `api_status`, `api_status_dsc`, `referal`, `unique_id`, `client_ip`, `client_exists`, `is_send`) VALUES (NULL, '0000-00-00 00:00:00', '', 'Julija', 'Alaine2', '".(time()+20)."', '12345627', 'ail2aine@alaine.lv', '1000', '12', '3', '', '1000', '', '', '12', 'manual', 'Pagaidīt šāmējie neko nezin vēl?!@$&G*F', '', '', '', '', '0');");
    	 
    	$cronPost = new CronPost1Lizings("http://localhost/app.onefingroup.com/web/api/post-data");
    	
    	$cronPost->run();
    	
    	$this->assertEquals($prevPersonCount+2, Person::find()->count());
    	$this->assertEquals($prevLoanCount+2, Loan::find()->count());
    	
    	$cronPost->run();
    	 
    	$this->assertEquals($prevPersonCount+2, Person::find()->count());
    	$this->assertEquals($prevLoanCount+2, Loan::find()->count());
    	
    	$loan = Loan::find()->orderBy("id desc")->one();
    	$this->assertEquals(1, $loan->product);
    	$this->assertEquals("Pagaidīt šāmējie neko nezin vēl?!@$&G*F", $loan->description);
    	$this->assertEquals(3, $loan->person->credit_history);
    }
    
    public function testApiOnefinance() {
    	sleep(2);
    	 
    	$prevPersonCount = Person::find()->count();
    	$prevLoanCount = Loan::find()->count();
    	 
    	$servername = ONEFINANCE_DB_HOST;
    	$serverdb = ONEFINANCE_DB_NAME;
    	$username = ONEFINANCE_DB_USER;
    	$password = ONEFINANCE_DB_PASSWORD;
    	$conn = new PDO("mysql:host=$servername;dbname=$serverdb", $username, $password);
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	//$conn->exec("TRUNCATE of5222_loan_forms;");
    	$conn->exec("INSERT INTO `of5222_loan_forms` (`gender`, `name`, `surname`, `personal_code`, `email`, `phone`, `credit_type`, `amount`, `term`, `first_payment`, `income`, `outcome`, `property_address`, `car_description`, `car_phone`, `car_owner`, `car_owner_address`, `car_workplace`, `car_position`, `car_work_experience`, `vsaa_statement`, `bank_account_statement`, `api_status`, `api_status_description`, `client_ip`, `referral`, `utm_vars`, `time`, `m_id`, `thanks`, `is_send`) VALUES
('m', 'Lauris', 'Liepa', '".(time()+22)."', 'liepa21@gmail.com', '+37126306422', 45, 4700.00, 12, 1500.00, 1200.00, 0.00, '', 'Automašīnas apraksts', '+37123232366', 'Lauris Liepa', 'Veldres 3, Rīga', 'LWS', 'darbinieks', '8 gadi', '', '51g78g293beb14j056d138_bank_account_statement.pdf', 'negative', 'Negatīvs lēmums:\nAtteikums saskaņā ar uzņēmuma kredītpolitiku.\nPieteikumā norādītais telefona numurs ir registrēts citam klientam (LAURIS LIEPA, 200785-11045, +37126306422).', '78.84.230.123', 'http://www.onefinance.lv/kredits/kreditu-apvienosana/?type=forma&m_id=b732807516e2794g93jbd0', '', '2016-05-23 14:06:39', '51g78g293beb14j056d138', 1, 0);");
    	
    	$cronPost = new CronPostOnefinance("http://localhost/app.onefingroup.com/web/api/post-data");
    	 
    	$cronPost->run();
    	 
    	$this->assertEquals($prevPersonCount+1, Person::find()->count());
    	$this->assertEquals($prevLoanCount+1, Loan::find()->count());
    	 
    	$cronPost->run();
    	
    	$this->assertEquals($prevPersonCount+1, Person::find()->count());
    	$this->assertEquals($prevLoanCount+1, Loan::find()->count());
    	 
    	$loan = Loan::find()->orderBy("id desc")->one();
    	$this->assertEquals(1, $loan->product);
    	$this->assertEquals(2, $loan->person->credit_history);
    	$this->assertEquals("", $loan->description);
    	
    	$this->assertEquals("m", $loan->person->gender);
    	$this->assertEquals("Automašīnas apraksts", $loan->extra->car_description);
    	$this->assertEquals("Lauris Liepa", $loan->extra->car_owner);
    	$this->assertEquals("+37123232366", $loan->extra->car_phone);
    	$this->assertEquals("Veldres 3, Rīga", $loan->extra->car_owner_address);
    	$this->assertEquals("LWS", $loan->extra->car_workplace);
    	$this->assertEquals("darbinieks", $loan->extra->car_position);
    	$this->assertEquals("8 gadi", $loan->extra->car_work_experience);
    	$this->assertEquals("", $loan->extra->property_address);
    	$this->assertEquals("", $loan->extra->vsaa_statement);
    	$this->assertEquals("51g78g293beb14j056d138_bank_account_statement.pdf", $loan->extra->bank_account_statement);
    	$this->assertEquals("Negatīvs lēmums:\nAtteikums saskaņā ar uzņēmuma kredītpolitiku.\nPieteikumā norādītais telefona numurs ir registrēts citam klientam (LAURIS LIEPA, 200785-11045, +37126306422).", $loan->extra->api_status_description);
    }
    
    public function testApiOnefinanceExtraInfo() {
    	sleep(2);
    
    	$prevPersonCount = Person::find()->count();
    	$prevLoanCount = Loan::find()->count();
    
    	$servername = ONEFINANCE_DB_HOST;
    	$serverdb = ONEFINANCE_DB_NAME;
    	$username = ONEFINANCE_DB_USER;
    	$password = ONEFINANCE_DB_PASSWORD;
    	$conn = new PDO("mysql:host=$servername;dbname=$serverdb", $username, $password);
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	//$conn->exec("TRUNCATE of5222_loan_forms;");
    	$pers = (time()+22);
    	$conn->exec("INSERT INTO `of5222_loan_forms` (`gender`, `name`, `surname`, `personal_code`, `email`, `phone`, `credit_type`, `amount`, `term`, `first_payment`, `income`, `outcome`, `property_address`, `car_description`, `car_phone`, `car_owner`, `car_owner_address`, `car_workplace`, `car_position`, `car_work_experience`, `vsaa_statement`, `bank_account_statement`, `api_status`, `api_status_description`, `client_ip`, `referral`, `utm_vars`, `time`, `m_id`, `thanks`, `is_send`) VALUES
('m', 'Lauris', 'Liepa', '".$pers."', 'liepa21@gmail.com', '+37126306422', 45, 4700.00, 12, 1500.00, 1200.00, 0.00, '', '', '', '', 'Veldres 1, Rīga', 'LWS', 'darbinieks', '8 gadi', '', '51g78g293beb14j056d138_bank_account_statement.pdf', 'negative', 'Negatīvs lēmums:\nAtteikums saskaņā ar uzņēmuma kredītpolitiku.\nPieteikumā norādītais telefona numurs ir registrēts citam klientam (LAURIS LIEPA, 200785-11045, +37126306422).', '78.84.230.123', 'http://www.onefinance.lv/kredits/kreditu-apvienosana/?type=forma&m_id=b732807516e2794g93jbd0', '', '2016-05-23 14:06:39', '51g78g293beb14j056d138', 1, 0);");
    	 
    	$cronPost = new CronPostOnefinance("http://localhost/crm/web/api/post-data");
    
    	$cronPost->run();
    
    	$this->assertEquals($prevPersonCount+1, Person::find()->count());
    	$this->assertEquals($prevLoanCount+1, Loan::find()->count());
    
    	$cronPost->run();
    	 
    	$this->assertEquals($prevPersonCount+1, Person::find()->count());
    	$this->assertEquals($prevLoanCount+1, Loan::find()->count());
    
    	$loan = Loan::find()->orderBy("id desc")->one();
    	$this->assertEquals(1, $loan->product);
    	$this->assertEquals(2, $loan->person->credit_history);
    	$this->assertEquals("", $loan->description);
    	 
    	$this->assertEquals("m", $loan->person->gender);
    	$this->assertEquals("", $loan->extra->car_description);
    	$this->assertEquals("", $loan->extra->car_owner);
    	$this->assertEquals("", $loan->extra->car_phone);
    	$this->assertEquals("Veldres 1, Rīga", $loan->extra->car_owner_address);
    	$this->assertEquals("LWS", $loan->extra->car_workplace);
    	$this->assertEquals("darbinieks", $loan->extra->car_position);
    	$this->assertEquals("8 gadi", $loan->extra->car_work_experience);
    	$this->assertEquals("", $loan->extra->property_address);
    	$this->assertEquals("", $loan->extra->vsaa_statement);
    	$this->assertEquals("51g78g293beb14j056d138_bank_account_statement.pdf", $loan->extra->bank_account_statement);
    	$this->assertEquals("Negatīvs lēmums:\nAtteikums saskaņā ar uzņēmuma kredītpolitiku.\nPieteikumā norādītais telefona numurs ir registrēts citam klientam (LAURIS LIEPA, 200785-11045, +37126306422).", $loan->extra->api_status_description);
    
    	$conn->exec("UPDATE  `lizings_wp`.`of5222_loan_forms` SET  `car_description` =  'test', `car_owner` =  'test2', `car_phone` =  'test3'  WHERE  `of5222_loan_forms`.`personal_code` = {$pers};");
    	 
    	$cronPost2 = new CronPostOnefinanceExtraInfo("http://localhost/app.onefingroup.com/web/api/post-data");
    	
    	$cronPost2->run();
    	
    	$loan2 = Loan::find()->orderBy("id desc")->one();
    	$this->assertEquals($loan->id, $loan2->id);
    	$this->assertEquals("test", $loan2->extra->car_description);
    	$this->assertEquals("test2", $loan2->extra->car_owner);
    	$this->assertEquals("test3", $loan2->extra->car_phone);
    	
    	$this->assertEquals($prevPersonCount+1, Person::find()->count());
    	$this->assertEquals($prevLoanCount+1, Loan::find()->count());
    }
}