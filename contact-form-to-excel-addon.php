<?php 
/*
Plugin Name: CF7 to Spreadsheet
Plugin URI:  http://localhost/plugin/wp-admin/plugins
Description: Save your contact form 7 data to google spreadsheet.
Version:     1.0
Author:      Abhijit
Author URI:  https://abhijits.sharkz.in
Text Domain: cf-7-to-spreadsheet
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once plugin_dir_path(__FILE__).'lib/autoload.php';
require_once plugin_dir_path(__FILE__).'lib/php-google-oauth/Google_Client.php';

/**
* Initialize the service request factory
*/ 
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

/**
 * Add plugin in admin setting menu.
 */
add_action( 'admin_menu','register_cf7_to_spreadsheet_setting' );
function register_cf7_to_spreadsheet_setting() {
    add_submenu_page( 
    	'options-general.php',								// Parent menu item slug
    	__( 'CF7 to Spreadsheet','cf-7-to-spreadsheet' ),	// Page Title
    	__( 'CF7 to Spreadsheet','cf-7-to-spreadsheet' ),	// Menu Title
    	'manage_options',									// Capability
    	'cf7-to-sheet',										// Menu Slug
    	'cf7_to_spreadsheet_setting_page'					// Callback function
        );
    add_action( 'admin_init', 'cf7_to_spreadsheet_register_setting' );
}

//register our settings
function cf7_to_spreadsheet_register_setting() {
	register_setting( 'cf7_to_spreadsheet_plugin_setting', 'cf7_to_spreadsheet_google_code' );
}

add_action( 'admin_init',  'cf7_to_spreadsheet_css' );
add_action( 'admin_print_styles', 'cf7_to_spreadsheet_css' );
/**
 * Register and enqueue our stylesheet.
 */
function cf7_to_spreadsheet_css(){
	wp_register_style( 'cf7_to_gs_style', plugins_url( "css/cf7-to-sheet-style.css",__FILE__ ) );
	wp_enqueue_style( 'cf7_to_gs_style' );
}

// Saving google spreadsheet data in db post meta table
add_action('wpcf7_after_save', 'save_cf7_to_spreadsheet_settings' );
function save_cf7_to_spreadsheet_settings( $post ) {
	update_post_meta( $post->id(), 'cf7_to_spreadsheet_data', $_POST['cf7-sheet'] );
}
/**
* Add new tab to contact form 7 editors panel
* @since 1.0
*/
add_filter( 'wpcf7_editor_panels', 'cf7_to_spreadsheet_editor_panels'  );
function cf7_to_spreadsheet_editor_panels( $panels ) {
	$panels[ 'google_sheets' ] = array(
  	'title' => __( 'CF7 to Spreadsheet', 'cf-7-to-spreadsheet' ),
  	'callback' =>'cf7_to_spreadsheet_editor_panel_google_sheet'
  	);
  	return $panels;
}

function cf7_to_spreadsheet_editor_panel_google_sheet( $post ) { 
	$form_id = sanitize_text_field( $_GET['post'] );
	$form_data = get_post_meta( $form_id, 'cf7_to_spreadsheet_data' ); ?>
    <div class="wrap cf7-fields">
    	<h2><?php __( 'Google Spreadsheet Settings','cf-7-to-spreadsheet' ); ?></h2>
		<p><?php __( 'Add your spreadsheet details here.','cf-7-to-spreadsheet' ); ?></p>
		<form  method="post" class="container">
			<table class="form-table">
		    	<tr valign="top">
		        	<th scope="row"><?php _e( 'Google Spreadsheet Name','cf-7-to-spreadsheet' ); ?></th>
		           	<td><input type="text" name="cf7-sheet[sheet-name]" class="large-text code" value="<?php echo ( isset ( $form_data[0]['sheet-name'] ) ) ? esc_attr( $form_data[0]['sheet-name'] ) : ''; ?>" />
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Google Spreadsheet Tab Name','cf-7-to-spreadsheet' ); ?></th>
		           	<td><input type="text" name="cf7-sheet[sheet-tab-name]" class="large-text code" value="<?php echo ( isset ( $form_data[0]['sheet-tab-name'] ) ) ? esc_attr( $form_data[0]['sheet-tab-name'] ) : ''; ?>"/>
		        </tr>
		    </table>
	  	</form>
	</div>
<?php }

function cf7_to_spreadsheet_setting_page() {?>
	<div class="wrap">
		<h1><?php _e( 'CF7 to Spreadsheet Setting','cf-7-to-spreadsheet' ); ?></h1>
		<form  action="options.php" method="post" class="container">
			<?php settings_fields( 'cf7_to_spreadsheet_plugin_setting' ); ?>
		    <?php do_settings_sections( 'cf7_to_spreadsheet_plugin_setting' ); ?>
		    <table class="form-table">
		    	<tr valign="top">
		        	<th scope="row"> <?php _e( 'Google Access Code','cf-7-to-spreadsheet' ); ?></th>
		        	<p class="description"> <?php _e( 'Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox.','cf-7-to-spreadsheet' ); ?></p>
		        	<td><input type="text" name="cf7_to_spreadsheet_google_code" required="required" value="<?php echo esc_attr( get_option('cf7_to_spreadsheet_google_code')) ?>"  /> 
		        		<a  href="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&state&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F" target="_blank" ><?php _e( 'Get Code ','cf-7-to-spreadsheet' ); ?></a>
		           	</td>
		        </tr>
		    </table>
		    <?php 
		    if ( isset ( $_GET['settings-updated'] ) && get_option( 'cf7_to_spreadsheet_google_code' ) != '' ){
				include_once( plugin_dir_path(__FILE__) . "lib/google-sheets.php" );
				googlespreadsheet::google_pre_authentication( get_option('cf7_to_spreadsheet_google_code') ); 
				//call method to preauthontication
				update_option('cf7_to_spreadsheet_google_code', null);
			}?>
		    <?php submit_button();?>
    	</form>
	</div>  
   <?php
}


add_action( 'wpcf7_mail_sent','cf7_to_spreadsheet' );
function cf7_to_spreadsheet( $cf7data ) {
	$submission = WPCF7_Submission::get_instance();
	$form_id = $cf7data->id();
	$form_data = get_post_meta( $form_id, 'cf7_to_spreadsheet_data' );
	$sheet_name = $form_data[0]['sheet-name'];
    $sheet_tab_name = $form_data[0]['sheet-tab-name'];
	$my_data = array();
    if ( $submission ) {
    	$posted_data = $submission->get_posted_data();
		include_once( plugin_dir_path(__FILE__) . "lib/google-sheets.php" );
		$doc = new googlespreadsheet();
		$doc->google_authentication();
		/**
		* Get spreadsheet by title
		*/		
		$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
		$spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();
		$spreadsheet = $spreadsheetFeed->getByTitle( $sheet_name );
		/**
		* Get particular worksheet of the selected spreadsheet
		*/
		$worksheetFeed = $spreadsheet->getWorksheets();
		$worksheet = $worksheetFeed->getByTitle( $sheet_tab_name );
		// adding date coloumn to  your spreadsheet
		$my_data["date"] = date('n/j/Y');
		foreach ( $posted_data as $key => $value ) {
			// exclude the default wpcf7 fields in object
			// handle strings and array elements
				if ( is_array( $value ) ) {
					$my_data[$key] = implode( ',', $value );	
				} else {
					$my_data[$key] = $value;
				}					
			}
		$listFeed = $worksheet->getListFeed();
		$listFeed->insert( $my_data );
	}
}