<?php


namespace app\components\utils;


use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Yii;

class StringUtils {

	static public function cleanPhone($phone) {
		$phone = trim($phone);
		$phone = str_replace(' ', '', $phone);
		$pos = strrpos($phone, '+371');

		if ($pos !== false && $pos === 0) {
			$phone = substr($phone, 4);
		}

		return $phone;
	}

    /**
     * @param $phone
     * @param string $region
     * @return string
     */
    static public function formatPhone($phone, string $region = 'LV'): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneObject  = $phoneUtil->parse($phone, $region);

            if (!$phoneUtil->isValidNumber($phoneObject)) {
                Yii::error('Invalid phone number: ' . $phone);
                return $phone;
            }

            return $phoneUtil->format($phoneObject, PhoneNumberFormat::E164);

        } catch (NumberParseException $e) {
            Yii::error('Phone number parsing error for: ' . $phone . '; '. $e->getMessage());
            return $phone;
        }
    }

}
