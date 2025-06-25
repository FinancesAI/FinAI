<?php

require (__DIR__ . "/init.php");
require (__DIR__ . "/../../cronpost/CronPost.php");
require (__DIR__ . "/../../cronpost/CronPost1Lizings.php");
require (__DIR__ . "/../../cronpost/CronPost1Aizdevums.php");
require (__DIR__ . "/../../cronpost/CronPostOnefinance.php");


use app\models\Loan;
use app\models\Person;
use app\models\LoanExtra;
use app\components\SolrDataProvider;
use app\models\Changes;
use app\models\User;

class BugFixTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{

	}

	public function tearDown()
	{

	}

	public function testApiIndexBug() {
		$prevPersonCount = Person::find()->count();
		$prevLoanCount = Loan::find()->count();
		$prevLoanExtraCount = LoanExtra::find()->count();
	
		$id = time();
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
				"id" => $id
		]);
	
		$loanId = Loan::find()->where(["unique_id" => "1aizdevums-".$id])->one();
	
		var_dump($loanId->id);
	
		// rādās pie new
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 1);
	
		// uzstāda reminder
		$loanId->reminder_time = time()+3000;
		$loanId->save();
	
		// update
		$cronPost = new CronPost("http://localhost/app.onefingroup.com/web/api/post-data");
		$cronPost->post([
				"Loan" => [
						"source" => "1aizdevums",
				],
				"LoanExtra" => [
						"car_phone"  => "12345679",
						"car_owner" => "Aluja",
				],
				"id" => $id,
				"task" => "update"
		]);
	
		// nerādās pie jaunajiem
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 1);
	
		// rādāš pie new updated
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:[1 TO *] AND !last_changed_field:reminder_time AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 0);
	
		// nerādās pie new ar reminder
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND last_changed_field:reminder_time AND reminder_time:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
	
		$this->assertEquals($newDataProvider->solr->itemsFound(), 0);
	
		$loanId->refresh();
		$this->assertEquals(0, $loanId->reminder_time);
	
		// atkal reminder time
		$loanId->status = 1;
		$loanId->reminder_time = time()+3000;
		$loanId->save();
		$changes = Changes::find()->where(["type_id" => $loanId->id, "attr" => "reminder_time"])->all();
		
		$user = User::find()->one();
		$userId = $user->id;
		foreach ($changes as $change) {
			$change->user_id = $userId;
			$change->save();
		}
		
		// auto update
		$loanId->skipReminder = true;
		$loanId->status = Loan::STATUS_NEW;
		$loanId->reminder_time = null;
		$loanId->save();
	
		$loanId->refresh();
		
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 1);
	
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:[1 TO *] AND !last_changed_field:reminder_time AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 0);
	
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND last_changed_field:reminder_time AND reminder_time:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
	
		$this->assertEquals($newDataProvider->solr->itemsFound(), 1);
	
		$this->assertEquals($prevPersonCount+1, Person::find()->count());
		$this->assertEquals($prevLoanCount+1, Loan::find()->count());
		$this->assertEquals($prevLoanExtraCount+1, LoanExtra::find()->count());
	}
	
	public function testApiIndexBug2() {
		$prevPersonCount = Person::find()->count();
		$prevLoanCount = Loan::find()->count();
		$prevLoanExtraCount = LoanExtra::find()->count();
	
		$id = time();
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
				"id" => $id
		]);
	
		$loanId = Loan::find()->where(["unique_id" => "1aizdevums-".$id])->one();
	
		// rādās pie new
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 1);

		// update
		$cronPost = new CronPost("http://localhost/app.onefingroup.com/web/api/post-data");
		$cronPost->post([
				"Loan" => [
						"source" => "1aizdevums",
				],
				"LoanExtra" => [
						"car_phone"  => "12345679",
						"car_owner" => "Aluja",
				],
				"id" => $id,
				"task" => "update"
		]);
	
		// nerādās pie jaunajiem
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 1);
	
		// rādāš pie new updated
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND changes_count:[1 TO *] AND !last_changed_field:reminder_time AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
		$this->assertEquals($newDataProvider->solr->itemsFound(), 0);
	
		// nerādās pie new ar reminder
		$newDataProvider = new SolrDataProvider();
		$newDataProvider->solr->setCollectionUrlByType(2);
		$newDataProvider->setClassName("app\models\Loan");
		$newDataProvider->solr->setQuery('status:0 AND last_changed_field:reminder_time AND reminder_time:0 AND id:'.$loanId->id);
		$newDataProvider->solr->retrieve();
	
		$this->assertEquals($newDataProvider->solr->itemsFound(), 0);
	}

}