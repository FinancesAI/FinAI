<?php


require (__DIR__ . "/init.php");

use app\models\Loan;
use app\models\Person;
use app\models\Changes;

class ChangesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        
    }
    
    public function tearDown()
    {
        
    }

    public function testLoanChanges()
    {
    	$loan = new Loan();
		$loan->load($this->_getPost("Juris", "juris@example.com", time()));
    	
    	$this->assertTrue($loan->save());
    	
    	$this->assertEquals(0, Changes::find()->where(["type" => Loan::TYPE, "type_id" => $loan->id])->count());
    	
    	$loan->amount = 100;
    	$loan->save();
    	$this->assertEquals(1, Changes::find()->where(["type" => Loan::TYPE, "type_id" => $loan->id])->count());
    	 
    	$loan->person->name = "Jurijs";
    	$loan->person->save();
    	// two becouse loan count updated
    	$this->assertEquals(2, Changes::find()->where(["type" => Person::TYPE, "type_id" => $loan->person->id])->count());
    	$this->assertEquals(1, Changes::find()->where(["type" => Loan::TYPE, "type_id" => $loan->id])->count());
    	 
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", array_merge($loan->actions, [sprintf("%02d", 2)]));
    	$this->assertTrue($loan->save());
    	$this->assertEquals(2, Changes::find()->where(["type" => Loan::TYPE, "type_id" => $loan->id])->count());
    	//$this->assertEquals("02", $loan->actions);
    	 
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", array_merge($loan->actions, [sprintf("%02d", 3)]));
    	$this->assertTrue($loan->save());
    	$this->assertEquals(3, Changes::find()->where(["type" => Loan::TYPE, "type_id" => $loan->id])->count());
    	//$this->assertEquals("02,03", $loan->actions);
    	
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", array_merge($loan->actions, [sprintf("%02d", 2)]));
    	$this->assertTrue($loan->save());
    	$this->assertEquals(4, Changes::find()->where(["type" => Loan::TYPE, "type_id" => $loan->id])->count());
    	//$this->assertEquals("02,03", $loan->actions);
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