<?php 
/*
Plugin Name: CF7 to Spreadsheet
Plugin URI: http://www.brainstormforce.com
Description: Save your contact form 7 data to google spreadsheet.
Version: 1.0
Author: Brainstorm Force     
Author URI: https://www.brainstormforce.com/
Text Domain: cf-7-to-spreadsheet
*/

//Block direct access to plugin files
defined( 'ABSPATH' ) or die();

require_once plugin_dir_path(__FILE__).'lib/autoload.php';
require_once plugin_dir_path(__FILE__).'lib/php-google-oauth/Google_Client.php';

if(!class_exists("Cf7_to_spreadsheet")){
	
	class Cf7_to_spreadsheet{
		public function __construct(){
			// Add settings to admin menu. 
			add_action( 'admin_menu', array ( $this, 'register_cf7_to_spreadsheet_setting' ) );
			// Register your settings 
	    	add_action( 'admin_init', array ( $this, 'cf7_to_spreadsheet_register_setting' ) );
	    	// Load plugin textdomain
			add_action( 'init', array ( $this, 'cf7_to_spreadsheet_load_textdomain' ) );
			// Add admin notice for CF7 inactive
			add_action( 'admin_notices', array ( $this,'confirm_cf7_activate' ) );
			// Add new tab to contact form 7 editors panel
			add_filter( 'wpcf7_editor_panels', array ( $this, 'cf7_to_spreadsheet_editor_panels'),10,1 );
			// Save spreadsheet settings from CF7 after submit
			add_action('wpcf7_after_save', array ( $this,'save_cf7_to_spreadsheet_settings' ) );
			// Add data to spreadsheet after CF& mail sent
			add_action( 'wpcf7_mail_sent', array ( $this,'cf7_to_spreadsheet_send_data' ),10,1 );
		}

		/**
		* Function Name: register_cf7_to_spreadsheet_setting
		* Function Description: Add plugin in admin setting menu
		*/
		function register_cf7_to_spreadsheet_setting() {
		    add_submenu_page( 
		    	'options-general.php',								// Parent menu item slug
		    	__( 'CF7 to Spreadsheet','cf-7-to-spreadsheet' ),	// Page Title
		    	__( 'CF7 to Spreadsheet','cf-7-to-spreadsheet' ),	// Menu Title
		    	'manage_options',									// Capability
		    	'cf7-to-sheet',										// Menu Slug
		    	array ($this,'cf7_to_spreadsheet_setting_page')				// Callback function
		        );
			// Register and enqueue our stylesheet.
			wp_register_style( 'cf7_to_gs_style', plugins_url( "css/cf7-to-sheet-style.css",__FILE__ ) );
			wp_enqueue_style( 'cf7_to_gs_style' );
		}

		/**
		* Function Name: confirm_cf7_activate
		* Function Description: Notice for contact form activation
		*/
		function confirm_cf7_activate(){
			if ( !function_exists( 'wpcf7' ) ) {
				$network_url = network_admin_url('plugin-install.php?s=contact+form+7&tab=search&type=term');
				echo "<div class='error'><p>CF7 to Spreadsheet requires <a href=".$network_url."> Contact Form7</a> is installed and activated. </p></div>";
			}	
		}

		/**
		* Function Name: cf7_to_spreadsheet_register_setting
		* Function Description: Register our settings.
		*/
		function cf7_to_spreadsheet_register_setting() {
			register_setting( 'cf7_to_spreadsheet_plugin_setting', 'cf7_to_spreadsheet_google_code' );
		}

		/**
		* Function Name: cf7_to_spreadsheet_load_textdomain
		* Function Description: Load plugin textdomain.
		* @since 1.0.0
		*/
		function cf7_to_spreadsheet_load_textdomain() {
			load_plugin_textdomain( 'cf-7-to-spreadsheet' ); 
		}

		/**
		* Function Name: save_cf7_to_spreadsheet_settings
		* Function Description: Saving google spreadsheet data in db post meta table
		* @param object $post
		*/
		function save_cf7_to_spreadsheet_settings( $post ) {
			update_post_meta( $post->id(), 'cf7_to_spreadsheet_data', $_POST['cf7-sheet'] );
		}
			
		/**
		* Function Name: cf7_to_spreadsheet_editor_panels
		* Function Description: Add new tab to contact form 7 editors panel
		* @since 1.0
		* @param array $panels
		* @return array $panels
		*/
		function cf7_to_spreadsheet_editor_panels( $panels ) {
			$panels[ 'google_sheets' ] = array(
				'title'    => __( 'CF7 to Spreadsheet', 'cf-7-to-spreadsheet' ),
				'callback' =>array ($this,'cf7_to_spreadsheet_editor_panel_google_sheet')
		  	);
		  	return $panels;
		}

		/**
		* Function Name: cf7_to_spreadsheet_editor_panel_google_sheet
		* Function Description: Saving google spreadsheet data
		* @param object $post
		*/
		function cf7_to_spreadsheet_editor_panel_google_sheet( $post ) { 
			$form_id   = sanitize_text_field( $_GET['post'] );
			$form_data = get_post_meta( $form_id, 'cf7_to_spreadsheet_data' );
			ob_start();
		    include_once (plugin_dir_path(__FILE__)."include/cf7-editor-panel-html.php");
		    $editor_panel_html = ob_get_contents();
			ob_end_clean();
		    echo $editor_panel_html;
		}

		/**
		* Function Name: cf7_to_spreadsheet_setting_page
		* Function Description: Register google access code
		*/
		function cf7_to_spreadsheet_setting_page() {
			include_once (plugin_dir_path(__FILE__)."include/cf7-setting-page-html.php");
			ob_start();
			$setting_page_html = ob_get_contents();
			ob_end_clean();
		    echo $setting_page_html;
			if ( isset ( $_GET['settings-updated'] ) && get_option( 'cf7_to_spreadsheet_google_code' ) != '' ){
				include_once( plugin_dir_path(__FILE__). "lib/class-google-spreadsheet.php" );
				//call method to preauthontication
				google_spreadsheet::google_pre_authentication( get_option('cf7_to_spreadsheet_google_code'));
				update_option('cf7_to_spreadsheet_google_code', null);
			}
		}

		/**
		* Function Name: cf7_to_spreadsheet_send_data
		* Function Description: Sending contact form 7 data to google spreadsheet.
		* @param object $cf7data
		*/
		function cf7_to_spreadsheet_send_data( $cf7data ) {
			$submission     = WPCF7_Submission::get_instance();
			$form_id        = $cf7data->id();
			$form_data      = get_post_meta( $form_id, 'cf7_to_spreadsheet_data' );
			$sheet_name     = $form_data[0]['sheet-name'];
		    $sheet_tab_name = $form_data[0]['sheet-tab-name'];
			$my_data = array();
		    if ( $submission ) {
		    	$posted_data = $submission->get_posted_data();
				include_once( plugin_dir_path(__FILE__) . "lib/class-google-spreadsheet.php" );
				$doc = new google_spreadsheet();
				$doc->google_authentication();
				// Get spreadsheet by title				
				$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
				$spreadsheetFeed    = $spreadsheetService->getSpreadsheetFeed();
				$spreadsheet        = $spreadsheetFeed->getByTitle( $sheet_name );
				// Get particular worksheet of the selected spreadsheet
				$worksheetFeed      = $spreadsheet->getWorksheets();
				$worksheet          = $worksheetFeed->getByTitle( $sheet_tab_name );
				// adding date coloumn to  your spreadsheet
				$my_data["date"]    = date('n/j/Y');
				foreach ( $posted_data as $key => $value ) {
					// exclude the default wpcf7 fields in object
					// handle strings and array elements
						if ( is_array( $value ) ) {
							$my_data[$key] = implode( ',', $value );	
						} else {
							$my_data[$key] = $value;
						}					
					}
				// Inserting data to spreadsheet.
				$listFeed = $worksheet->getListFeed();
				$listFeed->insert( $my_data );
			}
		}
	}
	
	new Cf7_to_spreadsheet();
}