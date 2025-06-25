<?php


require (__DIR__ . "/init.php");

use app\models\Loan;
use app\models\Person;
use app\models\Changes;

class LoanTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        
    }
    
    public function tearDown()
    {
        
    }
    
    public function testActions() {
    	$_POST = [
    			"Loan" => [
    					"amount"  => "2000",
    					"term" => "24",
    			],
    			"Person" => [
    					"name" => "Jānis",
    					"surname" => "Bērzs",
    					"personal_code" => "123456-1245125",
    					"phone" => "5125125",
    					"email" => "janis@example.com",
    					"income" => "2000",
    					"outcome" => "500",
    					"dependants" => "5",
    			]
    	];
    	
    	$loan = new Loan();
    	$loan->load($_POST);
    	
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 0);

    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("status", 1);
    	$loan->setAttribute("description", "test");
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 2);
    	
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("description", "test2sad");
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 3);
    	
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", ["66" => 1, "70" => 1, "71" => 1]);
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 6);
    	
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", ["01" => 1, "66" => 1, "70" => 1, "71" => 1]);
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 7);
    	
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", ["02" => 1, "66" => 1, "70" => 1, "71" => 1]);
    	$this->assertSame($loan->actions["66"], 1);
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 8);

    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("actions", ["01" => 1, "66" => 0, "70" => 1, "71" => 1]);
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 10);
    	
    	$loan = Loan::find()->where(["id" => $loan->id])->one();
    	$loan->setAttribute("approved", 1);
    	$this->assertTrue($loan->save());
    	$this->assertSame((int)Changes::find()->where(["type_id" => $loan->id, "type" => Loan::TYPE])->count(), 11);
    }
    
    public function testCloseTime() {
    	$_POST = [
    			"Loan" => [
    					"amount"  => "1000",
    					"term" => "12",
    			],
    			"Person" => [
    					"name" => "TestName",
    					"surname" => "TestSurname",
    					"personal_code" => "123456-12345612",
    					"phone" => "1234567890",
    					"email" => "TestName@example.com",
    					"income" => "1000",
    					"outcome" => "1000",
    					"dependants" => "1",
    			]
    	];
    	 
    	$loan = new Loan();
    	$loan->load($_POST);
    	 
    	$this->assertTrue($loan->save());
    	
    	$loan->status = (string)Loan::STATUS_REJECTED;
    	$loan->deal_stage = 1;
    	 
    	$this->assertTrue($loan->save());
    	$this->assertRegExp('/'.substr(time(), 0, -2).'/', "".$loan->close_time."");
    	
    	$loan->status = Loan::STATUS_IN_PROGRES;
    	
    	$this->assertTrue($loan->save());
    	$this->assertSame(null, $loan->close_time);

    	$loan->status = Loan::STATUS_CLOSED;
    	 
    	$this->assertTrue($loan->save());
    	$prevTime = time();

    	$this->assertRegExp('/'.substr($prevTime, 0, -2).'/', "".$loan->close_time."");
    	 
    	$loan->description = "test";
    	
    	sleep(2);
    	$this->assertTrue($loan->save());
    	$this->assertRegExp('/'.substr($prevTime, 0, -2).'/', "".$loan->close_time."");
    	 
    	$loan->status = Loan::STATUS_IN_PROGRES;
    	
    	$this->assertTrue($loan->save());
    	$this->assertSame(null, $loan->close_time);
    	
    	$loan->status = Loan::STATUS_CLOSED;
    	$loan->deal_stage = 1;
    	$loan->deal_product = 1;
    	
    	$prevTime = time();
    	$this->assertTrue($loan->save());
    	$this->assertSame($prevTime, $loan->close_time);
    	sleep(2);
    	 
    	$loan->deal_stage = 1;
    	$loan->deal_product = 2;
    	$loan->status = Loan::STATUS_CLOSED;
    	 
    	$this->assertTrue($loan->save());
    	sleep(2);
    	$this->assertSame($prevTime, $loan->close_time);
    	
    	$loan->status = Loan::STATUS_IN_PROGRES;
    	 
    	$this->assertTrue($loan->save());
    	$this->assertSame(null, $loan->close_time);

    	$loan->description = "test";
    	
    	$this->assertTrue($loan->save());
    	$this->assertSame(null, $loan->close_time);

    	$loan->description = "test2s";
    	$loan->status = Loan::STATUS_CLOSED;
    	 
    	$this->assertTrue($loan->save());

    	$this->assertRegExp('/'.substr(time(), 0, -2).'/', "".$loan->close_time."");
    	 
    	$loan->status = Loan::STATUS_IN_PROGRES;
    	 
    	$this->assertTrue($loan->save());
    	
    	$loan->status = Loan::STATUS_IN_PROGRES;
    	$loan->description = "test22s";
    	 
    	$this->assertTrue($loan->save());

    	$loan->description = "test2s";
    	$loan->status = (string)Loan::STATUS_CLOSED;
    	$loan->deal_stage = 1;
    	$loan->deal_product = 3;
    	
    	$this->assertTrue($loan->save());

    	$this->assertRegExp('/'.substr(time(), 0, -2).'/', "".$loan->close_time."");
    }
    
    public function testLoan()
    {
    	$_POST = [
    		"Loan" => [
    				"amount"  => "1000",
    				"term" => "12",
    		],
    		"Person" => [
    				"name" => "TestName",
    				"surname" => "TestSurname",
    				"personal_code" => "123456-123456",
    				"phone" => "1234567890",
    				"email" => "TestName@example.com",
    				"income" => "1000",
    				"outcome" => "1000",
    				"dependants" => "1",
    		]
    	];
    	
    	$loan = new Loan();
		$loan->load($_POST);
    	
    	$this->assertTrue($loan->save());

    	$this->assertTrue((boolean)$loan->person_id);
    	$this->assertSame(null, $loan->close_time);
    	$id = $loan->id;
    	$person_id = $loan->person_id;
    	
    	$loan = Loan::find()->where(["id" => $id])->one();
    	$loan->deal_stage = 2;
    	$loan->save();

    	$this->assertSame(2, $loan->deal_stage);

    	$this->assertFalse((boolean)$loan->close_time);
    	
    	$loan = Loan::find()->where(["id" => $id])->one();
    	$loan->deal_stage = 0;
    	$this->assertTrue($loan->save());
    	$this->assertSame(null, $loan->close_time);
    	$this->assertSame($person_id, $loan->person_id);

//     	$closeTime = time();
//     	$loan = Loan::find()->where(["id" => $id])->one();
//     	$loan->deal_stage = 1; // ??? on 0 wont save
//     	$loan->close_time = $closeTime;
//     	$this->assertTrue($loan->save());
    	
//     	$loan2 = Loan::find()->where(["id" => $id])->one();
//     	$this->assertSame($closeTime, $loan2->close_time);
    	 
    	$this->assertEquals(count($loan->person->loans), Loan::find()->where(["person_id" => $loan->person_id])->count());
    }
}