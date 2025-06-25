<?php


namespace app\components\services;


class SendApiRequestHelper {

	public static function getDateOfBirthFromPersCode( $persCode ) {
		if ( empty( $persCode ) ) {
			return '';
		}
		$pers = explode( '-', $persCode );

		if ( ! count( $pers ) ) {
			return '';
		}

		$per = str_split( $pers[0], 2 );

		$day   = $per[0];
		$month = $per[1];
		$year  = $per[2];

		if ( $year < 40 ) {
			$year = "20$year";
		} else {
			$year = "19$year";
		}

		return $year . '-' . $month . '-' . $day;
	}
}
