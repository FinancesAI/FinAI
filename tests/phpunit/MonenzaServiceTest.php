<?php
require (__DIR__ . "/init.php");

//namespace app\components\services;

use app\components\services\MonenzaService;
use app\models\Loan;
use PHPUnit\Framework\TestCase;

class MonenzaServiceTest extends TestCase {

	protected function setUp() {

	}

	public function testGetPurpose() {
		$this->assertEquals( 1, 1 );
	}

	public function testHandleAppRequestGoodResponse() {
		$monezaService = new MonenzaService( Loan::findOne( 5 ) );

		$response = json_decode( '{"applicationId":722,"clientStatus":"NEW"}', true );

		$monezaService->handleApplicationRequestResponse( $response );
	}

	public function testHandleAppRequestErrorResponse() {
		$monezaService = new MonenzaService( Loan::findOne( 1 ) );

		$response = json_decode( '{"errors":[{"property":"mobilePhone","errorCode":"incorrect.format"}]}', true );

		$monezaService->handleApplicationRequestResponse( $response );
	}

	public function testHandleAppApproveGoodResponse() {
		$monezaService = new MonenzaService( Loan::findOne( 5 ) );

		$response = json_decode( '{"loginUrl":"http://moneza.lv?janka"}', true );

		$monezaService->handleApplicationApproveResponse( $response );
	}




}
