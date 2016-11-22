<?php 
/*
Plugin Name: CF7 to Spreadsheet
Plugin URI:  http://localhost/plugin/wp-admin/plugins
Description: Save your contact form 7 data to google spreadsheet.
Version:     1.0
Author:      Abhijit
Author URI:  https://abhijits.sharkz.in
Text Domain: bsf-form
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
add_action( 'admin_menu','register_cf7_to_sheet_setting' );
function register_cf7_to_sheet_setting() {
    add_submenu_page( 
    	'options-general.php',	// Parent menu item slug
    	'CF7 to Spreadsheet',	// Page Title
    	'CF7 to Spreadsheet',	// Menu Title
    	'manage_options',		// Capability
    	'cf7-to-sheet',		// Menu Slug
    	'cf7_setting_page'		// Callback function
        );
    add_action( 'admin_init', 'wp_bsf_form_register_setting' );
}
//register our settings
function wp_bsf_form_register_setting() {
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_google_code' );
}

add_action( 'admin_init',  'cf7_to_gs_css' );
add_action( "admin_print_styles", 'cf7_to_gs_css' );
/**
 * Register and enqueue our stylesheet.
 */
function cf7_to_gs_css(){
 	wp_register_style('cf7_to_gs_style', plugins_url("css/cf7-to-sheet-style.css",__FILE__));
    wp_enqueue_style( 'cf7_to_gs_style' );
}


add_action('wpcf7_after_save', 'save_cf7_gs_settings' );
function save_cf7_gs_settings( $post ) {
	update_post_meta( $post->id(), 'cf7_to_sheet_save', $_POST['cf7-sheet'] );
}
/**
* Add new tab to contact form 7 editors panel
* @since 1.0
*/
add_filter( 'wpcf7_editor_panels', 'cf7_to_sheet_editor_panels'  );
function cf7_to_sheet_editor_panels( $panels ) {
  $panels[ 'google_sheets' ] = array(
  	'title' => __( 'CF7 To Spreadsheet', 'contact-form-7' ),
    'callback' =>'cf7_editor_panel_google_sheet'
  );

  return $panels;
}

function cf7_editor_panel_google_sheet( $post ) { 
	$form_id = sanitize_text_field( $_GET['post'] );
	$form_data = get_post_meta( $form_id, 'cf7_to_sheet_save' ); ?>
	<form method="post">
         <div class="cf7_field_gs">
            <h2><span>Google Spreadsheet Settings</span></h2>
            <p>
            <label>Google Spreadsheet Name:</label>
            <input type="text" name="cf7-sheet[sheet-name]" value="<?php echo ( isset ( $form_data[0]['sheet-name'] ) ) ? esc_attr( $form_data[0]['sheet-name'] ) : ''; ?>" />
            </p>
            <p>
            <label>Google Spreadsheet Tab Name:</label>
            <input type="text" name="cf7-sheet[sheet-tab-name]" value="<?php echo ( isset ( $form_data[0]['sheet-tab-name'] ) ) ? esc_attr( $form_data[0]['sheet-tab-name'] ) : ''; ?>"/>
            </p>
        </div>
      </form>
<?php }

function cf7_setting_page() {?>
	<div class="wrap">
		<h1>CF7 to Spreadsheet Setting</h1>
		<form  action="options.php" method="post" class="container">
			<?php settings_fields( 'bsf_form_plugin_settings' ); ?>
		    <?php do_settings_sections( 'bsf_form_plugin_settings' ); ?>
		    <table class="form-table">
		    	<tr valign="top">
		        	<th scope="row">Google Access Code</th>
		        	<p class="description">Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox.</p>
		        	<td><input type="text" name="bsf_form_google_code" required="required" value="<?php echo esc_attr( get_option('bsf_form_google_code')) ?>"  /> 
		        	<a  href="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&state&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F" target="_blank" >Get Code</a>
		           	</td>
		        </tr>
		    </table>
		    <?php 
		    if(isset($_GET['settings-updated']) && get_option('bsf_form_google_code') != ''){
				include_once(plugin_dir_path(__FILE__) . "lib/google-sheets.php");
				googlesheet::preauth( get_option('bsf_form_google_code') ); 
				//call method to preauthontication
				update_option('bsf_form_google_code', null);
				update_option('bsf_form_google_set', 1);
				}?>
		    <?php submit_button();?>
    	</form>
	</div>  
   <?php
}


add_action('wpcf7_mail_sent','bsf_form_wpcf7_to_sheet');

function bsf_form_wpcf7_to_sheet($cfdata) {
	$submission = WPCF7_Submission::get_instance();
	$form_id = $cfdata->id();
	$form_data = get_post_meta( $form_id, 'cf7_to_sheet_save' );
	$sheet_name = $form_data[0]['sheet-name'];
    $sheet_tab_name = $form_data[0]['sheet-tab-name'];
	$my_data = array();
    if ($submission) {
    	try{
        $posted_data = $submission->get_posted_data();
			include_once(plugin_dir_path(__FILE__) . "lib/google-sheets.php");
		$doc = new googlesheet();
		$doc->auth();
		/**
		* Get spreadsheet by title
		*/		
		$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
		$spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();
		$spreadsheet = $spreadsheetFeed->getByTitle($sheet_name);
		/**
		* Get particular worksheet of the selected spreadsheet
		*/
		$worksheetFeed = $spreadsheet->getWorksheets();
		$worksheet = $worksheetFeed->getByTitle($sheet_tab_name);
		$my_data["date"] = date('n/j/Y');
		foreach ( $posted_data as $key => $value ) {
			// exclude the default wpcf7 fields in object
			// handle strings and array elements
				if (is_array($value)) {
					$my_data[$key] = implode(',', $value);	
				} else {
					$my_data[$key] = $value;
				}					
			}
		$listFeed = $worksheet->getListFeed();
		$listFeed->insert($my_data);
	} catch (Exception $e) {
			$data['ERROR_MSG'] = $e->getMessage();
			$data['TRACE_STK'] = $e->getTraceAsString();
			googlesheet::cf7_to_sheet_debug_log($data);
		}
	}
}