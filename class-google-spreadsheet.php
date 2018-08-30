<?php

require_once plugin_dir_path(__FILE__).'lib/php-google-oauth/Google_Client.php';
require_once ( plugin_dir_path(__FILE__) . 'lib/autoload.php' );

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
if ( ! class_exists( 'Cgs_Google_Spreadsheet' ) ) {

	class Cgs_Google_Spreadsheet {

		/**
		 * Class instance.
		 * @since 1.1.0
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * @since 1.1.0
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	
		/**
		 *   @since 1.1.0
		 *  Constructor
		 */
		public function __construct() {
			$token;
			$spreadsheet;
			$worksheet;
			
		}

		const redirect     = 'urn:ietf:wg:oauth:2.0:oob';

		/**
		* Function Name: get_api_key
		* @since 1.1.0
		* @return api key array $data 
		* Function Description: Get the api key from database.
		*/
		public static function get_api_key(){
			$clientid = get_option('clientidkey');
			$clientsecret = get_option('clientsecretkey');
			if(!empty($clientid))
			{
				$data['clientid'] = $clientid;
			}
			if(!empty($clientsecret)){
				$data['clientsecret'] = $clientsecret; 
			}
			return $data ;
		}
		/**
		* Function Name: google_connect_url
		* Function Description: Generate google connect url.
		*/
		public static function google_connect_url () {
			$client_data = Cgs_Google_Spreadsheet::get_api_key();
			$cf7_google_url  = '';
			$cf7_google_url .= 'https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&';
			$cf7_google_url .= 'client_id='.$client_data['clientid'];
			$cf7_google_url .= '&redirect_uri='.Cgs_Google_Spreadsheet::redirect;
			$cf7_google_url .= '&state&scope=https://spreadsheets.google.com/feeds';
			return $cf7_google_url;
		}

		/**
		* Function Name: google_pre_authentication
		* Function Description: Pre-authenticate entered access code.
		* @param object $access_code
		*/
		public static function google_pre_authentication( $access_code ) {
			$client_data = Cgs_Google_Spreadsheet::get_api_key();
			$client = new Google_Client();
			$client->setClientId( $client_data['clientid'] );
			$client->setClientSecret( $client_data['clientsecret'] );
			$client->setRedirectUri( Cgs_Google_Spreadsheet::redirect );
			$client->setScopes( array( 'https://spreadsheets.google.com/feeds' ) );
			try{
			$results = $client->authenticate( $access_code );
			}
			catch(Exception $e) {
				global $error_message_code;
				$error_message_code = '<span style="color:red;">Error: Entered code is incorrect, please enter a valid code.</span></br> </br>';
			}
			$token_data = json_decode( $client->getAccessToken(), true );
			Cgs_Google_Spreadsheet::update_token( $token_data );
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
			$client_data = Cgs_Google_Spreadsheet::get_api_key();
			$token_data = json_decode( get_option( 'cf7_to_spreadsheet_google_token' ), true );	
			if( time() > $token_data['expire'] ) {
				$client = new Google_Client();
				$client->setClientId( $client_data['clientid'] );
				$client->setClientSecret(  $client_data['clientsecret'] );
				$client->refreshToken( $token_data[ 'refresh_token' ] );
				$token_data = array_merge( $token_data, json_decode( $client->getAccessToken(), true ) );
				Cgs_Google_Spreadsheet::update_token( $token_data );
			}
			$accessToken = $token_data[ 'access_token' ];
			$serviceRequest = new DefaultServiceRequest( $accessToken );
			ServiceRequestFactory::setInstance( $serviceRequest );
		}
	}
}
Cgs_Google_Spreadsheet::get_instance();
