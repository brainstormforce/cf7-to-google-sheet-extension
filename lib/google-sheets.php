<?php
require_once plugin_dir_path(__FILE__).'php-google-oauth/Google_Client.php';
include_once ( plugin_dir_path(__FILE__) . 'autoload.php' );
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class googlesheet {
	private $token;
	private $spreadsheet;
	private $worksheet;
	const clientId = '448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com';
	const clientSecret = 'kIAz_RGoD6hSGZyYKmIaSZGh';
	const redirect = 'urn:ietf:wg:oauth:2.0:oob';


	public static function preauth( $access_code ){		
		$client = new Google_Client_New();
		$client->setClientId( googlesheet::clientId );
		$client->setClientSecret( googlesheet::clientSecret );
		$client->setRedirectUri( googlesheet::redirect );
		$client->setScopes( array( 'https://spreadsheets.google.com/feeds' ) );
		$results = $client->authenticate( $access_code );
		$tokenData = json_decode( $client->getAccessToken(), true );
		googlesheet::updateToken( $tokenData );
	}
	
	public static function updateToken( $tokenData ){
		$tokenData['expire'] = time() + intval( $tokenData['expires_in'] );
		try{
			$tokenJson = json_encode( $tokenData );
			update_option( 'cf7_to_spreadsheet_google_token', $tokenJson );
		} catch ( Exception $e ) {
			echo "error";;
		}
	}
	
	public function auth(){
		$tokenData = json_decode( get_option( 'cf7_to_spreadsheet_google_token' ), true );	
		if( time() > $tokenData['expire'] ){
			$client = new Google_Client_New();
			$client->setClientId( googlesheet::clientId );
			$client->setClientSecret( googlesheet::clientSecret );
			$client->refreshToken( $tokenData['refresh_token'] );
			$tokenData = array_merge( $tokenData, json_decode( $client->getAccessToken(), true ) );
			googlesheet::updateToken( $tokenData );
		}
		/* this is needed */
		$accessToken = $tokenData['access_token'];
		$serviceRequest = new DefaultServiceRequest( $accessToken );
		ServiceRequestFactory::setInstance( $serviceRequest );
	}
}
