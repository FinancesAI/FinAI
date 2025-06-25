<?php


require (__DIR__ . "/init.php");

use app\models\Field;
use app\models\Person;
use app\models\Loan;

class FieldTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
		Yii::$app->cache->flush();
    }
    
    public function tearDown()
    {
    	Yii::$app->cache->flush();
    }
    
    /**
     * @todo bad values relation
     */
    public function testFieldLoanPersonError () {
//     	$field = new Field();
//     	$field->name = "person.15t12ffs";
//     	$field->type = Loan::TYPE;
//     	$field->role = 0;
//     	$field->enable = 1;
//     	$this->assertFalse((boolean)$field->save());
    	
//     	$field = new Field();
//     	$field->name = "person.15t12ffs";
//     	$field->type = Person::TYPE;
//     	$field->role = 0;
//     	$field->enable = 1;
//     	$this->assertFalse((boolean)$field->save());
    }
    
    public function testFieldLoanPerson () {
    	$field = new Field();
    	$field->name = "person.id";
    	$field->type = Loan::TYPE;
    	$this->assertTrue((boolean)$field->save());
    	
    	$this->assertTrue((boolean)$field->delete());
    }
    
    public function testFieldCreateDublicateIfNotExists2() {
    	$fieldCreateTime = Field::find()->where(["name" => "amount", "type" => Loan::TYPE])->one();
    	if ($fieldCreateTime)
    		Yii::$app->db->createCommand("DELETE FROM ".Field::tableName()." WHERE `id` = ".$fieldCreateTime->id)->execute();
    	
    	$field = new Field();
    	$field->name = "amount";
    	$field->type = Loan::TYPE;
    	$this->assertTrue((boolean)$field->save());
    	
    	$this->assertTrue((boolean)$field->delete(true));
    }
    
    public function testFieldCreateDublicateIfNotExists() {
    	$fieldsBefore = Field::find()->count();
    	 
    	$fieldCreateTime = Field::find()->where(["name" => "create_time", "type" => Person::TYPE])->one();
    	if ($fieldCreateTime)
    		Yii::$app->db->createCommand("DELETE FROM ".Field::tableName()." WHERE `id` = ".$fieldCreateTime->id)->execute();
    	
    	$field = new Field();
    	$field->name = "create_time";
    	$field->type = Person::TYPE;
    	 
    	$colsBefore = Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute();
    	
    	$this->assertTrue($field->save());
    	 
    	$this->assertEquals($colsBefore, Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute());
    	$this->assertEquals($fieldsBefore, Field::find()->count());
    }
    
    public function testFieldCreateDublicate()
    {
    	$fieldsBefore = Field::find()->count();
    	
    	$field = new Field();
    	$field->name = "create_time";
    	$field->type = Person::TYPE;
    	
    	$colsBefore = Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute();
    	 
    	$this->assertFalse($field->save());
    	
    	$this->assertEquals($colsBefore, Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute());
    	$this->assertEquals($fieldsBefore, Field::find()->count());
    }
    
    public function testRules () {
    	$name = time()."b";
    	 
    	$field = new Field();
    	$field->name = $name;
    	$field->type = Person::TYPE;
    	$this->assertTrue($field->save());
    	 
    	$person = new Person();
    	$rules = $person->rules();
    	
		// šito var notestēt uz personsearch klases.
    	//$this->assertTrue(in_array($name, $rules[7][0]));
    	//$this->assertEquals("safe", $rules[7][1]);
    	
    	$this->assertTrue($person->hasAttribute($name));
    	 
    	$person->name = "TestField";
    	$person->surname = "Custom";
    	$person->personal_code = time();
    	$person->email = "testing@example.com";
    	$person->phone = "123456";
    	$person->income = "1000";
    	$person->outcome = "5000";
    	$person->$name = "Test field value";
    	$this->assertTrue($person->save());
    	$personId = $person->id;
    	$person = Person::find()->where(["id" => $personId])->one();
    	$this->assertEquals($person->$name, "Test field value");
    	
    	$this->assertTrue($field->delete());
    }
    
    public function testFieldCreate()
    {
    	$name = time()."a";
    	
		$field = new Field();
    	$field->name = $name;
    	$field->type = Person::TYPE;
    	$colsBefore = Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute();
    	
    	$this->assertTrue($field->save());
    	$this->assertEquals($colsBefore+1, Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute());
    	
    	$field->filter_type = 1;
    	$this->assertTrue($field->save());
    	$this->assertEquals($colsBefore+1, Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute());
    	
    	$this->assertTrue($field->delete());
    	$this->assertEquals($colsBefore, Yii::$app->db->createCommand("SHOW COLUMNS from ".Person::tableName().";")->execute());
    }
}