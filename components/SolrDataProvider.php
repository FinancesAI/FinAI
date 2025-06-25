<?php
namespace app\components;

use yii\data\BaseDataProvider;
use app\components\Solr;

use yii\db\Expression;

class SolrDataProvider extends BaseDataProvider
{
    /**
     * 
     * @var Solr
     */
    public $solr;
    
    public $modelQuery;
    
    /**
     *
     * @var Solr
     */
    public $className;
    
    public function __construct($config = [])
    {
    	parent::__construct($config);
    	
    	$this->solr = new Solr();
    }
    
    public function setClassName($class)
    {
    	$this->className = $class;
    }
    
    public function setModelQuery($query)
    {
    	$this->modelQuery = $query;
    }
    
    /**
     * (non-PHPdoc)
     * @see \yii\data\BaseDataProvider::prepareModels()
     */
    protected function prepareModels ()
    {
        $this->_retrieve();
        
        $className = $this->className;
        
        $models = "";

        $this->totalCount = $this->solr->itemsFound();
        $this->pagination->totalCount = $this->solr->itemsFound();
        foreach($this->solr->getList() as $item)
        {
            $models .= $item->id.", ";
        }
        $models = rtrim($models, ", ");
        
        if (!$models) {
        	return [];
        }

        return $className::find()->where('id IN('.$models.')')->orderBy([new Expression('FIELD (id, ' . $models . ')')])->all();
    }
    
    /**
     * (non-PHPdoc)
     * @see \yii\data\BaseDataProvider::prepareKeys()
     */
    protected function prepareKeys ($models)
    {
        $this->_retrieve();
        
        $keys = [];

        foreach($this->solr->getList() as $item)
        {
            $keys[] = $item->id;
        }

        return $keys;
    }
    
    /**
     * (non-PHPdoc)
     * @see \yii\data\BaseDataProvider::prepareTotalCount()
     */
    protected function prepareTotalCount ()
    {
        $this->_retrieve ();
        
        return $this->solr->itemsFound();
    }
    
    /**
     * @throws \Exception
     * @return void
     */
    private function _retrieve ()
    {
        if (!$this->solr->response())
        {
            if (!$this->solr->retrieve())
            {
                throw new \Exception("cannot retrieve solr data");
            }
        }
    }
}
