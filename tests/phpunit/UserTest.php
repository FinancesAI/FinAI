<?php


require (__DIR__ . "/init.php");

use app\models\User;

class UserTest extends PHPUnit_Framework_TestCase
{
	private $_user;
	
    public function setUp()
    {
        
    }
    
    public function tearDown()
    {

    }
    
    public function testPasswordOnChange()
    {
    	$password = "testtest";
    	
    	$user = new User();
		$user->fullname = "Jānis Ails";
		$user->email = time()."@example.com";
		$user->password = $password;
		$user->role = 0;
    	
    	$this->assertTrue($user->save());
    	$id = $user->id;
    	
    	$this->assertSame($user->password, crypt($password, Yii::$app->params["salt"]));

    	$user = User::find()->where(["id" => $id])->one();
    	$user->fullname = "Jānis Alils";
    	
    	$this->assertTrue($user->save());
    	$this->assertSame($id, $user->id);
    	$this->assertSame($user->password, crypt($password, Yii::$app->params["salt"]));

    	$user = User::find()->where(["id" => $id])->one();
    	$password = "test2test2";
    	$user->password = $password;
    	 
    	$this->assertTrue($user->save());
    	$this->assertSame($id, $user->id);
    	$this->assertSame($user->password, crypt($password, Yii::$app->params["salt"]));
    	
    	$user = User::find()->where(["id" => $id])->one();
    	$user->fullname = "test";
    	
    	$this->assertTrue($user->save());
    	$this->assertSame($id, $user->id);
    	$this->assertSame($user->password, crypt($password, Yii::$app->params["salt"]));
    	
    	$this->_user = $user;
    }
}