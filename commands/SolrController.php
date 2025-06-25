<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

use app\components\Solr;
use app\models\ShopProduct;
use app\models\Loan;
use app\models\Person;

/**
 * Solr indexing
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SolrController extends Controller
{
	public $limit = 100;
	
	/**
	 * Run commands
	 */
	public function actionRun ()
	{
		ob_start();
		$this->actionCreateCommands();
		$command = ob_get_contents();
		
		return shell_exec ($command);
	}
	
	/**
	 * delete all
	 */
	public function actionDeleteAll ()
	{
		$solr = new Solr();
		$solr->deleteAll(1);
		$solr->deleteAll(2);
	}

	/**
	 * delete all
	 */
	public function actionCount()
	{
		$solr = new Solr();
		$solr->setQuery("*:*");
		$solr->retrieve();
		echo $solr->itemsFound()."\r\n";
	}
	
	/**
	 * Print command
	*/
	public function actionCreateCommands ()
	{
		$loans = Loan::find()->count();
		$persons = Person::find()->count();

		$command = "";
		
		for ($x = 0; $x <= $loans/$this->limit; $x++)
			$command .= "./yii solr/index-loan ".$x."; ";
		
		for ($x = 0; $x <= $persons/$this->limit; $x++)
			$command .= "./yii solr/index-person ".$x."; ";

		$command .= "\r\n";
	
		echo $command;
	}
	
	/**
	 * Index loan
	 */
	public function actionIndexLoan ($part)
	{
		$solr = new Solr();
		$solr->setCollectionUrlByType("2");
		
		$offset = ($part)*$this->limit;

		$cities = Loan::find()->offset($offset)->limit($this->limit)->all();
		
		foreach ($cities as $city)
		{
			echo "Indexing loan - ".$city->id."\r\n";
	
			$solr->indexByModel($city);
		}
	}
	
	/**
	 * Index persons
	 */
	public function actionIndexPerson ($part)
	{
		$solr = new Solr();
		$solr->setCollectionUrlByType("1");
		
		$offset = ($part)*$this->limit;
		$cities = Person::find()->offset($offset)->limit($this->limit)->all();
		
		foreach ($cities as $city)
		{
			echo "Indexing person - ".$city->id."\r\n";
	
			$solr->indexByModel($city);
		}
	}
	
	public function actionIndex()
	{
		$solr = new Solr();
		$solr->setCollectionUrlByType("2");
		
		$cities = Loan::find()->where(["need_reindex" => 1])->all();
		
		foreach ($cities as $city)
		{
			if ($solr->indexByModel($city)) {
				\Yii::$app->db->createCommand("UPDATE `loan` SET `need_reindex` = '0' WHERE `loan`.`id` ={$city->id};")->execute();
			}
		}
	}
}