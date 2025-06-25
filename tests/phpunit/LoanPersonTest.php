<?php


require (__DIR__ . "/init.php");

use app\models\Loan;
use app\models\Person;

class LoanPesonTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        
    }
    
    public function tearDown()
    {
        
    }

    public function testLoan()
    {
    	$beforeUser = Person::find()->where(["personal_code" => "123456-123456222"])->one();
    	if ($beforeUser) {
    		$before = $beforeUser->loan_count;
    	} else {
    		$before = 0;
    	}
    	
    	$loan = new Loan();
		$loan->load($this->_getPost("Juris", "juris@example.com", "123456-123456222"));
    	
    	$this->assertTrue($loan->save());

    	$this->assertTrue((boolean)$loan->person_id);
    	
    	$this->assertEquals("Juris", $loan->person->name);
    	 
    	$loan2 = new Loan();
    	$loan2->load($this->_getPost("Jūlija", "julija@example.com", "123456-123456222	"));
    	 
    	$this->assertTrue($loan2->save());
    	
    	$this->assertTrue((boolean)$loan2->person_id);
    	
    	$this->assertEquals($loan->person_id, $loan2->person_id);
    	 
    	$checkPerson = Person::find()->where(["id" => $loan->person_id])->one();
    	$this->assertEquals(time(), $checkPerson->update_time);
    	 
    	$this->assertEquals("Jūlija", $checkPerson->name);
    	$this->assertEquals("julija@example.com", $checkPerson->email);
    	
    	$afterUser = Person::find()->where(["personal_code" => "123456-123456222"])->one();
    	$after = $afterUser->loan_count;
    	
    	$this->assertEquals($before+2, $after);
    }
    
    private function _getPost($personName, $email, $personal) {
    	unset($_POST);
    	$_POST = [
    		"Loan" => [
    				"amount"  => "1000",
    				"term" => "12",
    		],
    		"Person" => [
    				"name" => $personName,
    				"surname" => "TestSurname",
    				"personal_code" => $personal,
    				"phone" => "1234567890",
    				"email" => $email,
    				"income" => "1000",
    				"outcome" => "1000",
    				"dependants" => "1",
    		]
    	];
    	return $_POST;
    }
}