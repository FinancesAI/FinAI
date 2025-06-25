<?php


require (__DIR__ . "/init.php");

use app\models\Loan;
use app\models\Person;
use app\models\Changes;
use app\models\LoanExtra;

class LoanExtraTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        
    }
    
    public function tearDown()
    {
        
    }

    public function testLoanMultipleFiles()
    {
    	$_POST = [
    		"Loan" => [
    				"amount"  => "1000",
    				"term" => "12",
    		],
    		"Person" => [
    				"name" => "Karlis",
    				"surname" => "Jampams",
    				"personal_code" => "511252-123456",
    				"phone" => "1621614124",
    				"email" => "karlis@example.com",
    				"income" => "1550",
    				"outcome" => "200",
    				"dependants" => "3",
    		]
    	];
    	
    	$loan = new Loan();
		$loan->load($_POST);
    	
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 0);
    	 
    	$this->assertSame($loan->extra->loan_id, $loan->id);
    	
    	$loan->extra->bank_account_statement = [
    			0 => "first.pdf",
    			1 => "second.pdf"
    	];
    	
    	$this->assertTrue($loan->extra->save());
    	
    	$loan->status = Loan::STATUS_REJECTED;
    	
    	$this->assertTrue($loan->save());
    	
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 1);
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => LoanExtra::TYPE])->count(), 2);
    	
    	$this->assertSame($loan->extra->bank_account_statement, "second.pdf");
    	
    	$loan->extra->bank_account_statement = "third.pdf";
    	 
    	$this->assertTrue($loan->extra->save());
    	
    	$this->assertSame($loan->extra->bank_account_statement, "third.pdf");
    	
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => LoanExtra::TYPE])->count(), 3);
    	
    	$loan->extra->api_status_description = "test";
    	
    	$this->assertTrue($loan->extra->save());
    	 
    	$this->assertSame($loan->extra->bank_account_statement, "third.pdf");
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => LoanExtra::TYPE])->count(), 4);
    	 
    }
}