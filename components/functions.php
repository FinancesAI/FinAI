<?php

use app\services\SourceService;

function dd( $data ) {
	yii\helpers\VarDumper::dump( $data, 10, true );
	\Yii::$app->end();
}

function mapFormToFields( $formData ) {
	$data = null;

	$formId = $formData["form_id"];
	$loan   = unserialize( $formData['form_value'] );

	switch ( $formId ) {
		case 1752: //paterina kredits
			return [
				"Loan"   => [
					"amount"        => (double) $loan['suma'],
					"term"          => (int) $loan['srok'],
//					"source"        => \Yii::$app->params['wordpress']['forms'][ $formData["form_id"] ],
                    "source"        => SourceService::getWordpressForms()[ $formData["form_id"] ],
					"product"       => 1,
					"description"   => '',
					"first_payment" => 0,
					"referral"      => '',
					"query_string"  => '',
					"ip_ountry"     => '',
				],
				"Person" => [
					"name"           => $loan["imia"],
					"surname"        => '',
					"personal_code"  => $loan['personal-code'],
					"phone"          => $loan["phone"],
					"email"          => $loan["email"],
					"income"         => (double) $loan['zarplata'],
					"outcome"        => (double) $loan['platez'],
					"credit_history" => 0,
					"dependants"     => 0,
				],
				"id"     => $formData["form_id"],
			];
		case 1722:
			return [];
		default:
			return [];
	}
}