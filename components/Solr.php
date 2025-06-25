<?php
namespace app\components;

use Yii;
use yii\base\Component;

use app\models\Article;
use yii\web\HttpException;
use yii\log\Logger;
use app\models\Shop;
use app\models\ShopProduct;
use app\models\Loan;
use app\models\Person;
use app\models\Changes;
use yii\base\BaseObject;
class Solr extends BaseObject // Component
{
    /**
     * @var string
     */
    public $modelClass;
    
    /**
     * response writers rw
     * @var string
     */
    public $format = "json";
    
    /**
     * get parameters send to server
     * @var array
     */
    protected $_requestParamaters = [];

    /**
     * set data retrieved
     * json object
     * @see retrieve()
     * @var string
     */
    protected $_response = null;
    
    /**
     * set request url
     * @var stringt
     */
    protected $_collectionUrl = null;
    
    /**
     * Solr constructor
     * @return void
     */
    public function init()
    {
        parent::init();

        if (\Yii::$app->className() !== "yii\console\Application") {
        	$page = \Yii::$app->request->get("page");
        } else {
        	$page = 1;
        }
        
        if (!$page) {
        	$page = 1;
        }
        
        $this->_requestParamaters["q"] = "*:*";
        $this->_requestParamaters["start"] = (int)($page-1)*\Yii::$app->setting->get("page_limit");
        $this->_requestParamaters["rows"] = \Yii::$app->setting->get("page_limit");
        $this->_requestParamaters["fl"] = "id";
        $this->_requestParamaters["wt"] = $this->format;
        $this->_requestParamaters["hl"] = "true";
        $this->_requestParamaters["hl.fl"] = "text";
    }
	
    /**
     * set order for request
     * @return void
     */
    public function setOrder($order)
    {
        $this->_requestParamaters["sort"] = $order;
    }

    /**
     * set query
     * @param string $query
     * @param boolean $extra
     * @return void
     */
    public function setQuery($query, $extra = false)
    {
    	if ($extra) {
    		$this->_requestParamaters["q"] = $this->_requestParamaters["q"]." ".$query;
    	} else {
    		$this->_requestParamaters["q"] = $query;
    	}

        Yii::trace("set query: " . $this->_requestParamaters["q"]);
    }

    /**
     * get query
     * @return string
     */
    public function getQuery()
    {
    	return $this->_requestParamaters["q"];
    }

    /**
     * get query
     * @return string
     */
    public function select($attrs)
    {
    	//@todo
    }
	
	/**
     * set filter query
     * @param string $query
     * @return void
     */
    public function setFilterQuery($query)
    {
        $this->_requestParamaters["fq"] = $query;

        Yii::trace("set filter query: " . $query);
    }
	
	/**
     * set filter query
     * @param string $query
     * @return void
     */
    public function setCustomFilter($param, $value)
    {
        $this->_requestParamaters[$param] = $value;

        Yii::trace("set filter query: param: " . $param." -  value: ".$value);
    }

    /**
     * retrieve data from object
     * send request parameters to solr instance
     * select/ query
     * @return boolean
     */
    public function retrieve($handler = "select", $context=null, $directQuery = null, $json=true)
    {
        Yii::trace("Solr retrieve: " . $handler);
        
		if (!$context)
        {
			$context = stream_context_create([
				'http'=>[
					'method'=>"GET",
					'header'=>"Accept-language: en\r\nAuthorization: Basic YXJ0dXJzOm15cGFzczE=\r\n",
					'timeout' => 7
				]
			]);
		}
        
        $request = $this->_collectionUrl . $handler . "?";

        if($directQuery)
        {
            $request .= $directQuery;
        }
        else
        {
            foreach($this->_requestParamaters as $parameter=>$value)
            {
                $request .= $parameter . "=" . urlencode($value) . "&";
            }
        }

        Yii::trace("Solr request: " . $request);

        $response = @file_get_contents($request, false, $context);
		if ($response === false)
		{
		    Yii::trace("solr error response", Logger::LEVEL_ERROR);
			throw new HttpException(500, Yii::t('app', 'Search failed, try again later.'));
		}

		Yii::trace("Solr response: " . $response);
        
		if (!$json)
			return $response;
		else
			$this->_response = json_decode($response);

        return true;
    }
    
    /**
     * Set url
     * @param integer $type
     */
	public function setCollectionUrlByType($type) {
		if ($type == 1)
			$url = \Yii::$app->params["solrPerson"];
		if ($type == 2)
			$url = \Yii::$app->params["solrLoan"];
		
		$this->_collectionUrl = $url;
	}
	
	/**
	 * Trigger solr commit
	 */
	public function commit()
	{
		$this->retrieve('update', null, 'commit=true');
	}
	
	/**
	 * update object
	 * @param array $data
	 * @return boolean
	 */
    public function atomicUpdate ($data)
    {
        if (empty($data) || !is_array($data))
        {
            throw new \Exception("bad parameter");
        }
        
        \Yii::trace("json atomic update: " . json_encode($data));
        
        $contextOptions = [
            'http' => [
            'method' => "POST",
            'header'=> "Content-type: application/json\r\nAuthorization: Basic YXJ0dXJzOm15cGFzczE=\r\n"
                . "Content-Length: " . strlen(json_encode($data)) . "\r\n",
                'content' => json_encode($data)
            ]
        ];
        
        $context = stream_context_create ($contextOptions);

        if (!$this->retrieve ("update", $context))
        {
        	Yii::error("cannot index model");
        	 
            return false;
        }
        // commit
        return $this->commit();
    }
	
	/**
     * delete item
     * @param string $category
     * @param 
     * @return boolean
     */
    public function delete($id, $type)
    {
		if (!$this->retrieve('update', null, 'stream.body='.urlencode('<delete><query>(id:'.$type.'-'.$id.')</query></delete>').'&commit=true&wt=json'))
		{
            throw new \Exception("cannot delete row");
		}
		
		return true;
    }
    
    /**
     * delete all by type
     * @param string $type
     * @return boolean
     */
    public function deleteAll($type)
    {
    	$this->setCollectionUrlByType($type);
        if ($this->retrieve('update', null, 'stream.body='.urlencode('<delete><query>*:*</query></delete>').'&commit=true&wt=json'))
        {
            return true;
        }
        return false;
    }

    /**
     * set offset for request
     * @param integer $offset
     * @return void
     */
    public function setOffset($offset)
    {
        if (!is_numeric($offset))
        {
            throw new Exception("bad offset");
        }

        $this->_requestParamaters["start"] = intval($offset);
    }

    /**
     * set limit
     * @param integer $limit
     * @return void
     */
    public function setLimit($limit) {

        if (!is_numeric($limit))
        throw new Exception("bad limit data type");

        $this->_requestParamaters["rows"] = intval($limit);
    }

    /**
     * get ietems total found
     * @return integer
     */
    public function itemsFound()
    {
        return intval($this->_response->response->numFound);
    }

    /**
     * get list of objects in array
     * parse received solr answer
     * @return array
     */
    public function getList()
    {
        return $this->_response->response->docs;
    }
	
    /**
     * Solr response
     * @return string
     */
	public function response()
	{
		return $this->_response;
	}

    /**
     * escape solr special chars
     * @param string $query
     * @return string
     */
    public static function escape ($query)
    {
		$match = ['\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' '];
		$replace = ['\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ '];
		$query = str_replace($match, $replace, $query);

		return $query;
    }
	
	/**
     * unescape solr special chars
     * @param string $query
     * @return string
     */
    public static function unescape ($query)
    {
		$match  = ['\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ '];
		$replace = ['\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' '];
		$query = str_replace($match, $replace, $query);

		return $query;
    }
	
	/**
     * remove solr special chars
     * @param string $query
     * @return string
     */
    public static function removeSpecialChars ($query)
    {
        $specials = ["<", ">", "+", "-", "&", "", "|", "!", "(", ")", "{", "}", "[", "]", "^", "\"", "~", "*", "?", ":", "\\"];
        
        return str_replace($specials, '', $query);
    }
    
    /**
     * set fields 
     * @param string $fileds
     * @return void
     */
    public function setFields ($fields)
    {
        $this->_requestParamaters["fl"] = $fields;
    }
    
    /**
     * index model
     * @param Loan $model
     * @return boolean
     */
    public function indexByModel($model)
    {
    	$type = $model::TYPE;
    	if ($type == Loan::TYPE) {
    		$files = 0;
    		$desc = null;
    		
    		if ($model->extra) {
    			if ($model->extra->bank_account_statement) {
    				$files = 1;
    			}
    			if ($model->extra->vsaa_statement) {
    				$files = $files+1;
    			}
    			if ($model->extra->vsaa_statement_ep52) {
    				$files = $files+1;
    			}
    		}
    		
    		$descriptions = Changes::find()
    		->where(["type" => Loan::TYPE])
    		->andWhere(["type_id" => $model->id])
    		->andWhere(["attr" => "description"])
    		->all();
    		
    		if ($descriptions) {
    			foreach ($descriptions as $description) {
    				$desc .= $description->attr_to." ";
    			}
    		}
    		$sort = 7;
    		if ($model->product == 2) {
    			$sort = 1;
    		}
    		if ($model->product == 1) {
    			$sort = 1;
    		}
    		if ($model->product == 5) {
    			$sort = 1;
    		}
    		if ($model->product == 3) {
    			$sort = 1;
    		}
    		if ($model->product == 4) {
    			$sort = 1;
    		}
    		if ($model->product == 6) {
    			$sort = 6;
    		}
    		
    		
    		$attrs = [
	                "id" => $model->id,
    				"amount" => $model->amount,
	        		"term" => $model->term,
	        		"user_id" => (int)$model->user_id,
	        		"approved" => $model->approved,
	        		"status" => $model->status,
    				"product" => $model->product,
    				"sproduct" => $sort,
    				"deal_stage" => $model->deal_stage,
	        		"deal_product" => $model->deal_product,
	        		"create_time" => $model->create_time,
	        		"update_time" => $model->update_time,
	        		"reminder_time" => $model->reminder_time,
	        		"close_time" => (int)$model->close_time,
	        		"waiting_time" => (int)$model->waiting_time,
    				
    				"loan_count" => Loan::find()->where(["person_id" => $model->person_id])->count(),
    				"files" => $files,
    				"desc" => $desc,
    				
	        		"lang" => $model->lang,
	        		"actions" => $model->actions,
	        		"referral" => $model->referral,
	        		"query_string" => $model->query_string,
	        		"first_payment" => (int)$model->first_payment,
	        		"source" => $model->source,
	        		"ip_ountry" => $model->ip_ountry,
	        		"changes_count" => Changes::find()->where(["type" => $model::TYPE, "type_id" => $model->id])->count(),
    				"person_id" => $model->person_id,
	        		"last_changed_field" => $model->last_changed_field,
	        		"rating" => $model->rating,
    				"updated" => time(),
    				"bill_status" => $model->getBillStatus(),
    				"bill_amount" => $model->getBillAmount(),
    		];
    	}
    	
    	if ($type == Person::TYPE) {
    		$attrs = [
    				"id" => $model->id,
    				"name" => $model->name,
    				"surname" => $model->surname,
    				"personal_code" => $model->personal_code,
    				"income" => $model->income,
    				"outcome" => $model->outcome,
    				"phone" => $model->phone,
    				"email" => $model->email,
    				"gender" => $model->gender,
    				"dependants" => $model->dependants,
    				"credit_history" => $model->credit_history,
    				"loan_count" => $model->loan_count,
    				"create_time" => $model->create_time,
    				"update_time" => $model->update_time,
    				"updated" => time(),
    		];
    	}
    	
    	try {
    		$this->atomicUpdate ([$attrs]);
    	} catch (\Exception $e) {
    		// @todo same for person
    		if ($type == Loan::TYPE) {
    			\Yii::$app->db->createCommand("UPDATE `loan` SET `need_reindex` = '1' WHERE `loan`.`id` ={$model->id};")->execute();
    		}
    		return false;
    	}
        
        return true;
    }
    
    /**
     * Build query for solr
     * @param unknown $array
     * @param unknown $modelClass
     */
    public function buildQuery($array, $modelClass) {
    	// reset query
    	$this->setQuery("");

    	$fq = "";
    	foreach ($array as $title => $value) {
    		if ($value !== null && $value !== "") {
    			if (strpos($title, 'person_') !== false) {
    				if (!$fq) {
    					$fq = "{!join from=id fromIndex=person to=person_id}".str_replace(["person_"], [""], $title).":{$value}";
    				} else {
    					$fq .= "";
    				}
    			} else {
    				if ($title == "create_time" || $title == "update_time" || $title == "close_time") {
    					$this->setQuery("(".$title.":[".strtotime($value)." TO ".strtotime('+1 day', strtotime($value))."])", true);
    				} else {
    					if (in_array($title, ["name", "surname", "personal_code", "email", "phone"])) {
    						$this->setQuery("(".$title.":".$value."*)", true);
    					} else {
    						$this->setQuery("(".$title.":".$value.")", true);
    					}
    				}
    			}
    		}
    	}
    	
    	if ($fq) {
    		$this->setFilterQuery($fq);
    	}
    	
    	if (isset($_GET["sort"])) {
    		$sort = $_GET["sort"];
    		
    	    	if (substr($sort, 0, 1) == "-") {
    				$sort = substr($sort, 1);
    				$this->setOrder($sort." DESC");
    			} else {
    				$this->setOrder($sort." ASC");
    			}
    	}
    	
    	if (!$this->getQuery()) {
    		$this->setQuery("*:*");
    	}
    }
}