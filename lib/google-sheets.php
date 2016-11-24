<?php
require_once plugin_dir_path(__FILE__).'php-google-oauth/Google_Client.php';
include_once ( plugin_dir_path(__FILE__) . 'autoload.php' );
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class googlespreadsheet {
	private $token;
	private $spreadsheet;
	private $worksheet;
	const clientId = '448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com';
	const clientSecret = 'kIAz_RGoD6hSGZyYKmIaSZGh';
	const redirect = 'urn:ietf:wg:oauth:2.0:oob';


	public static function google_pre_authentication( $access_code ){		
		$client = new Google_Client();
		$client->setClientId( googlespreadsheet::clientId );
		$client->setClientSecret( googlespreadsheet::clientSecret );
		$client->setRedirectUri( googlespreadsheet::redirect );
		$client->setScopes( array( 'https://spreadsheets.google.com/feeds' ) );
		$results = $client->authenticate( $access_code );
		$token_data = json_decode( $client->getAccessToken(), true );
		googlespreadsheet::update_token( $token_data );
	}
	
	public static function update_token( $token_data ){
		$token_data['expire'] = time() + intval( $token_data['expires_in'] );
		try{
			$tokenJson = json_encode( $token_data );
			update_option( 'cf7_to_spreadsheet_google_token', $tokenJson );
		} catch ( Exception $e ) {
			echo "error";;
		}
	}
	
	public function google_authentication(){
		$token_data = json_decode( get_option( 'cf7_to_spreadsheet_google_token' ), true );	
		if( time() > $token_data['expire'] ){
			$client = new Google_Client();
			$client->setClientId( googlespreadsheet::clientId );
			$client->setClientSecret( googlespreadsheet::clientSecret );
			$client->refreshToken( $token_data['refresh_token'] );
			$token_data = array_merge( $token_data, json_decode( $client->getAccessToken(), true ) );
			googlespreadsheet::update_token( $token_data );
		}
		/* this is needed */
		$accessToken = $token_data['access_token'];
		$serviceRequest = new DefaultServiceRequest( $accessToken );
		ServiceRequestFactory::setInstance( $serviceRequest );
	}
}
