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
    /**
     * 
[11:07:57 AM] roberts.batraks: a. alga 350-499, bet koeficients, ne lielāks/vienāds ar 0.2; 

b. alga 500-599, koeficients ne lielāks/vienāds 0,25, 

c. alga 600 un vairāk+ un koeficients ne lielāks/vienāds par 0,3

1. loan count ir mazāks vai vienāds ar 3

2. vecums 22+, bet ņem no esošās dienas + 1, piem., cilvēks dzimis 1994. g. 23. nov., šeit var skaitīt no 24. nov.! BET ne vecāks/vienāds ar 66 gadiem.

3. tikai naudas kredīts

4. credi history ne-negatīvs (citi var būt)


[11:08:10 AM] roberts.batraks: veidojas a+1+2+3+4
[11:08:17 AM] roberts.batraks: vai b+1+2+3+4
[11:08:24 AM] roberts.batraks: c +1+2+3+4

     */
    public function testActions() {
    	$_POST = [
    			"Loan" => [
    					"amount"  => "800",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jānis",
    					"surname" => "Bērzs",
    					"personal_code" => "281291-".time(),
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "200",
    					"outcome" => "0",
    					"dependants" => "5",
    			]
    	];
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, -1);

    	
    	
    	$_POST = [
    			"Loan" => [
    					"amount"  => "1200",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jānis",
    					"surname" => "Bērzs",
    					"personal_code" => "281291-".time(),
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "400",
    					"outcome" => "100",
    					"dependants" => "5",
    			]
    	];
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 0);
    	
    	sleep(1);
    	
    	$_POST = [];
    	$_POST = [
    			"Loan" => [
    					"amount"  => "2500",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jāniss",
    					"surname" => "Bērzs",
    					"personal_code" => "221291-".time(),
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "1000",
    					"outcome" => "10",
    					"dependants" => "5",
    			]
    	];
    	
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 1);
    	
    	
    	
    	
    	$_POST = [
    			"Loan" => [
    					"amount"  => "2500",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jāniss",
    					"surname" => "Bērzs",
    					"personal_code" => "221291-".time(),
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "100",
    					"outcome" => "10",
    					"dependants" => "5",
    			]
    	];
    	
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, -1);
    	$_POST = [
    			"Loan" => [
    					"amount"  => "500",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jāniss",
    					"surname" => "Bērzs",
    					"personal_code" => "221291-".time(),
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "100",
    					"outcome" => "10",
    					"dependants" => "5",
    			]
    	];
    	
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, -1);
    	
    	
    	$_POST = [
    			"Loan" => [
    					"amount"  => "1500",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jāniss",
    					"surname" => "Bērzs",
    					"personal_code" => "221299-".time(),
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "2000",
    					"outcome" => "10",
    					"dependants" => "5",
    			]
    	];
    	
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, -1);

    }
    
    public function testActions2() {
    	$personal = "221291-".time();
    	
    	$_POST = [
    			"Loan" => [
    					"amount"  => "2500",
    					"term" => "24",
    					"product" => 2,
    			],
    			"Person" => [
    					"name" => "Jānisssssss",
    					"surname" => "Bērzs",
    					"personal_code" => $personal,
    					"phone" => "5125125",
    					"accept_email" => 0,
    					"email" => "janis@example.com",
    					"income" => "1000",
    					"outcome" => "10",
    					"dependants" => "5",
    			]
    	];
    	
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 1);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 1);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 1);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 0);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 0);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 0);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, 0);
    	$loan = new Loan();
    	$loan->load($_POST);
    	$this->assertTrue($loan->save());
    	$this->assertSame($loan->rating, -1);
    }
    
    
    
    
    
    
}