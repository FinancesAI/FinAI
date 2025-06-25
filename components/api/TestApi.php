<?php
namespace app\components\api;

class TestApi extends ApiModel {

	public function __construct($model) {
		$this->setModel($model);
		$this->setTitle("Test api");
		$this->setContent($this->_getContent());
	}
	
	private function _getContent() {
		$html = "<h2>".$this->getLabel()."</h2>";
		$html .= "<p>Test api :)</p>";
		return $html;
	}
}