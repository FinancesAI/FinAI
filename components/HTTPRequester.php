<?php
/**
 * Created by PhpStorm.
 * User: Marcis
 * Date: 27.05.2019.
 * Time: 21:43
 */

namespace app\components;

/**
 * Taken from https://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php
 *
 * Class HTTPRequester
 * @package app\components
 */
class HTTPRequester {
    /**
     * @description Make HTTP-GET call
     *
     * @param       $url
     * @param array $params
     *
     * @return bool|string HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPGet( $url, array $params, $auth = null  ) {
        $query = http_build_query( $params );
        $ch    = curl_init( $url . '?' . $query );

        if ( $auth != null ) {
            curl_setopt( $ch, CURLOPT_USERPWD, $auth['username'] . ":" . $auth['password'] );
        }

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        $response = curl_exec( $ch );
        curl_close( $ch );

        return $response;
    }

    /**
     * @description Make HTTP-POST call
     *
     * @param       $url
     * @param array $params
     *
     * @param null $auth
     *
     * @return bool|string HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPPost( $url, array $params, $auth = null ) {
        $query = http_build_query( $params );
        $ch    = curl_init();

        //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
        //setting them to false.
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

        if ( $auth != null ) {
            curl_setopt( $ch, CURLOPT_USERPWD, $auth['username'] . ":" . $auth['password'] );
        }
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
        //curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );

        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );


        curl_setopt( $ch, CURLOPT_POSTFIELDS, $query );

        if(curl_error($ch)) {
            echo '--- error: --- ' . curl_error($ch);
            \Yii::error('CURL ERROR: ' . curl_error($ch));
        }

        $response = curl_exec( $ch );

        if (curl_error($ch)) {
            \Yii::error('CURL RESPONSE: ' . $response);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo '$httpcode: ' . $httpcode;

        if (curl_error($ch)) {
            \Yii::error('CURL HTTP CODE: ' . $httpcode);
        }

        curl_close( $ch );

        return $response;
    }

    public static function HTTPPostEncoded( $url, $params, $auth = null, $bearer = false) {
        $ch = curl_init();

        //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
        //setting them to false.
//		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
//		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

        if ( $auth != null ) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: ' . ($bearer ? 'Bearer ' : 'Basic ') . $auth
            ] );
        }
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

        $response = curl_exec( $ch );

        if ( curl_error( $ch ) ) {
            echo 'error:' . curl_error( $ch );
        }

        curl_close( $ch );

        return $response;
    }

    public static function HTTPPostBearer( $url, $params, $auth = null) {
        $ch = curl_init();

        //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
        //setting them to false.
//		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
//		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

        if ( $auth != null ) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $auth
            ] );
        }
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

        $response['response'] = curl_exec( $ch );

        $response['error'] = '';

        if ( curl_error( $ch ) ) {
            $response['error'] = curl_error( $ch );
//            echo 'error:' . curl_error( $ch );
        }

        $response['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close( $ch );

        return $response;
    }

    /**
     * @description Make HTTP-POST call
     *
     * @param       $url
     * @param string $data
     *
     * @return bool|string HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPPostString( $url, string $data ) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, ( $data ) );
        $response = curl_exec( $ch );
        curl_close( $ch );

        return $response;
    }

    /**
     * @description Make HTTP-PUT call
     *
     * @param       $url
     * @param array $params
     *
     * @return bool|string HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPPut( $url, array $params ) {
        $query = \http_build_query( $params );
        $ch    = \curl_init();
        \curl_setopt( $ch, \CURLOPT_RETURNTRANSFER, true );
        \curl_setopt( $ch, \CURLOPT_HEADER, false );
        \curl_setopt( $ch, \CURLOPT_URL, $url );
        \curl_setopt( $ch, \CURLOPT_CUSTOMREQUEST, 'PUT' );
        \curl_setopt( $ch, \CURLOPT_POSTFIELDS, $query );
        $response = \curl_exec( $ch );
        \curl_close( $ch );

        return $response;
    }

    /**
     * @param    $url
     * @param array $params
     *
     * @return bool|string HTTP-Response body or an empty string if the request fails or is empty
     * @category Make HTTP-DELETE call
     *
     */
    public static function HTTPDelete( $url, array $params ) {
        $query = \http_build_query( $params );
        $ch    = \curl_init();
        \curl_setopt( $ch, \CURLOPT_RETURNTRANSFER, true );
        \curl_setopt( $ch, \CURLOPT_HEADER, false );
        \curl_setopt( $ch, \CURLOPT_URL, $url );
        \curl_setopt( $ch, \CURLOPT_CUSTOMREQUEST, 'DELETE' );
        \curl_setopt( $ch, \CURLOPT_POSTFIELDS, $query );
        $response = \curl_exec( $ch );
        \curl_close( $ch );

        return $response;
    }
}
