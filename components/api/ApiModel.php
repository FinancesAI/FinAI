<?php

namespace app\components\api;

use app\models\Loan;
use app\models\LoanProgerss;

class ApiModel {
	public $title = "test2";
	public $content = "tes2t";
	public $model;
	public $data;

	public function getLabel() {
		return $this->title;
	}

	public function getContent() {
		return $this->content;
	}

	/**
	 * @return Loan
	 */
	public function getModel() {
		return $this->model;
	}

	public function setTitle( $title ) {
		$this->title = $title;
	}

	public function setModel( $model ) {
		$this->model = $model;
	}

	public function setSendData() {
		return null;
	}

	public function setContent( $content ) {
		$this->content = $content;
	}

	public function saveProgress( $result, $progressId ) {
		$loanProgress              = new LoanProgerss();
		$loanProgress->loan        = $this->getModel();
		$loanProgress->provider_id = $progressId;
		$loanProgress->status      = 3; // 3 - Approved
		$loanProgress->amount      = $this->getModel()->amount;

	}
}