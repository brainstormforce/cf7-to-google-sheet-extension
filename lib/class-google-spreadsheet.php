<?php

require_once plugin_dir_path(__FILE__).'php-google-oauth/Google_Client.php';
require_once ( plugin_dir_path(__FILE__) . 'autoload.php' );

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class google_spreadsheet {
	private $token;
	private $spreadsheet;
	private $worksheet;
	const clientId = '448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com';
	const clientSecret = 'kIAz_RGoD6hSGZyYKmIaSZGh';
	const redirect = 'urn:ietf:wg:oauth:2.0:oob';

	/**
	* Function Name: google_pre_authentication
	* Function Description: Pre-authenticate entered access code.
	* @param object $access_code
	*/

	public static function google_pre_authentication( $access_code ) {		
		$client = new Google_Client();
		$client->setClientId( google_spreadsheet::clientId );
		$client->setClientSecret( google_spreadsheet::clientSecret );
		$client->setRedirectUri( google_spreadsheet::redirect );
		$client->setScopes( array( 'https://spreadsheets.google.com/feeds' ) );
		$results = $client->authenticate( $access_code );
		$token_data = json_decode( $client->getAccessToken(), true );
		google_spreadsheet::update_token( $token_data );
	}
	
	/**
	* Function Name: update_token
	* Function Description: Updating access token .
	* @param object $token_data
	*/

	public static function update_token( $token_data ) {
		$token_data['expire'] = time() + intval( $token_data['expires_in'] );
		try{
			$tokenJson = json_encode( $token_data );
			update_option( 'cf7_to_spreadsheet_google_token', $tokenJson );
		} catch ( Exception $e ) {
			echo "error";
		}
	}
	
	/**
	* Function Name: google_authentication
	* Function Description: Authenticate before sending data to spreadsheet.
	*/

	public function google_authentication() {
		$token_data = json_decode( get_option( 'cf7_to_spreadsheet_google_token' ), true );	
		if( time() > $token_data['expire'] ) {
			$client = new Google_Client();
			$client->setClientId( google_spreadsheet::clientId );
			$client->setClientSecret( google_spreadsheet::clientSecret );
			$client->refreshToken( $token_data[ 'refresh_token' ] );
			$token_data = array_merge( $token_data, json_decode( $client->getAccessToken(), true ) );
			google_spreadsheet::update_token( $token_data );
		}
		$accessToken = $token_data[ 'access_token' ];
		$serviceRequest = new DefaultServiceRequest( $accessToken );
		ServiceRequestFactory::setInstance( $serviceRequest );
	}
}
