<?php 
/*
Plugin Name: BSF Form
Plugin URI:  http://localhost/plugin/wp-admin/plugins
Description: Simple form for submitting data to your google sheets.
Version:     2016-01
Author:      Abhijit
Author URI:  https://abhijits.sharkz.in
Text Domain: bsf-form
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Autoload files 
 *
 */ 
 require_once plugin_dir_path(__FILE__).'lib/vendor/autoload.php';
 require_once plugin_dir_path(__FILE__).'lib/vendor/php-google-oauth/Google_Client.php';

 
/**
* Initialize the service request factory
*/ 
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

/**
* Create shortcode for plugin
*/ 
function bsf_shortcode(){ ?>
	<form  action="" method="post">
		Enter your name: <input type="text" name="bsf_form_name" required="required" />
		Enter your email: <input type="email" name="bsf_form_email" required="required" /> <br /> <br />
		<input type="submit" name="bsf_form_submit" value="submit" />
	</form>   
	<?php
	if (isset($_POST['bsf_form_submit'])) {
		echo "Thank You!";
	}
}
add_shortcode('bsf', 'bsf_shortcode');

/**
 * Add plugin in menu page.
 */
add_action('admin_menu', 'wp_bsf_form_admin_page');
function wp_bsf_form_admin_page() {
	add_menu_page(
	__( 'Bsf Form list', 'textdomain' ),
	__( 'BSF Form','textdomain' ),
	'administrator',
	'bsf-form',
	'wp_bsf_form_callback',
	'dashicons-admin-plugins',
	'26'
	);
	add_action( 'admin_init', 'wp_bsf_form_register_setting' );
}

//register our settings
function wp_bsf_form_register_setting() {
	register_setting( 'bsf_form_plugin_settings', 'bs_form_shortcode' );
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_admin_email' );
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_google_code' );
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_google_client_id' );
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_google_client_email' );
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_google_sheet_title' );
	register_setting( 'bsf_form_plugin_settings', 'bsf_form_google_worksheet_tab' );
	register_setting( 'bsf_form_plugin_settings','bsf_form_google_token');
}


function wp_bsf_form_callback() {
	?>
	<div class="wrap">
		<h1>BSF FORM Settings</h1>
		<form  action="options.php" method="post" class="container">
			<?php settings_fields( 'bsf_form_plugin_settings' ); ?>
		    <?php do_settings_sections( 'bsf_form_plugin_settings' ); ?>
		    <table class="form-table">
		        <tr valign="top">
		        	<th scope="row">BSF Form Shortcode</th>
		        	<td><input type="text" name="
		        	" value="[bsf]" readonly="readonly" /> 
		        	<p class="description">Paste this code in your page</p>
		        	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row">Enter Admin Email Id</th>
		        	<td><input type="text" name="bsf_form_admin_email" required="required" value="<?php echo esc_attr( get_option('bsf_form_admin_email') ); ?>" />
		        	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row">Google Assess Code</th>
		        	<td><input type="text" name="bsf_form_google_code" required="required" value="><?php echo esc_attr( get_option('bsf_form_google_code')) ?>"  /><br /> 
		        	<p class="description">
				   	Click <a  href="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&state&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F" target="_blank">here</a>to allow us to access your spreadsheets.</p>
		           	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row">Google Sheet Title</th>
		        	<td><input type="text" name="bsf_form_google_sheet_title" required="required" value="<?php echo esc_attr( get_option('bsf_form_google_sheet_title') ); ?>" />
		        	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row">Google Sheet Tab</th>
		        	<td><input type="text" name="bsf_form_google_worksheet_tab" required="required" value="<?php echo esc_attr( get_option('bsf_form_google_worksheet_tab') ); ?>"  />
		        	</td>
		        </tr>
		        </table>
		    <?php 
		    if(isset($_GET['settings-updated']) && get_option('bsf_form_google_code') != ''){
				include_once(plugin_dir_path(__FILE__) . "lib/google-sheets.php");
				googlesheet::preauth( get_option('bsf_form_google_code') ); //call method to preauthontication
				update_option('bsf_form_google_code', null);
				update_option('bsf_form_google_set', 1);
				}
				?>
		    <?php submit_button(); ?>
    	</form>
	</div>  
   <?php
}

/**
* After form submit save data to db, send email and also save data to google sheets
*/	
if (isset($_POST['bsf_form_submit'])) {
	include_once(plugin_dir_path(__FILE__) . "lib/google-sheets.php");
	$bsf_form_google_code = get_option(bsf_form_google_code);
   	$bsf_form_google_client_id = get_option(bsf_form_google_client_id);
   	$bsf_form_google_client_email = get_option(bsf_form_google_client_email);
   	$bsf_form_google_sheet_title = get_option(bsf_form_google_sheet_title);
   	$bsf_form_google_worksheet_tab = get_option(bsf_form_google_worksheet_tab);
   	print_r(get_option(bsf_form_google_client_id));
 	$doc = new googlesheet();
	$doc->auth(); // authentication to connect to sheet
	/**
	 * Get spreadsheet by title
	 */		
	$spreadsheetTitle = $bsf_form_google_sheet_title;
	$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
	$spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();
	$spreadsheet = $spreadsheetFeed->getByTitle($spreadsheetTitle);
	/**
	 * Get particular worksheet of the selected spreadsheet
			
	 */
	$worksheetTitle = $bsf_form_google_worksheet_tab; // it's generally named 'Sheet1' 
	$worksheetFeed = $spreadsheet->getWorksheets();
	$worksheet = $worksheetFeed->getByTitle($worksheetTitle);$name = $_POST['bsf_form_name'];
	$email = $_POST['bsf_form_email'];
	$admin_email_id = $_POST['bsf_form_admin_email'];
	$data =  array( 
		$email => $name
	);
	if ( get_option( bsf_form_data ) !== false ) {
		$get_admin_email_id = get_option(bsf_form_admin_email);
		
		// updating option table
		if ($data) {
			$bsf_form_old_data = get_option(bsf_form_data);
			$combined_array = array_merge($data,$bsf_form_old_data);
			$bsf_update = update_option( bsf_form_data, $combined_array );
			if ( $bsf_update == true || $bsf_add_option == true) {
				$row = array('name'=>$name, 'email'=>$email);
				$listFeed = $worksheet->getListFeed();
				$listFeed->insert($row);
				
				/**
				* Sending Wp mail
				*/ 
				add_filter('init', 'wp_bsf_form_set_conent_for_mail');
				function wp_bsf_form_set_conent_for_mail() {
					$email = $_POST['bsf_form_email'];
					$name = $_POST['bsf_form_name'];
					$get_admin_email_id = get_option(bsf_form_admin_email);

					// send mail to subscriber
					$admin_email_id = $get_admin_email_id;
					$to = $email;
					$subject = 'Abhijits BSF';
					$message = "Your are register @BSF \r\n";
					$message .= "Thanking you";
					wp_mail( $to, $subject, $message );

					// send mail to administrator
					$to_bsf_admin = $admin_email_id;
					$subject = 'New register';
					$bsf_message = "New user Name-\t";
					$bsf_message .=$name."\r\n"; 
					$bsf_message .= "New user Email-\t";
					$bsf_message .=$email."\r\n"; 
					wp_mail( $to_bsf_admin, $subject, $bsf_message );
				}
			} else{
				/*echo "not upadate or add"*/;
			}
		}
	} else { 
		// adding new form data to option table
	    $bsf_add_option = add_option('bsf_form_data',$data,'', 'yes'); 
	}
}

