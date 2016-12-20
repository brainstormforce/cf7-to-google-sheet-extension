<!-- CF7 to spreadsheet setting page html form -->

<div class="wrap">
	<h1><?php _e( 'CF7 to Spreadsheet Setting','cf-7-to-spreadsheet' ); ?></h1>
	<form  action="options.php" method="post" class="container">
		<?php settings_fields( 'cf7_to_spreadsheet_plugin_setting' ); ?>
	    <?php do_settings_sections( 'cf7_to_spreadsheet_plugin_setting' ); ?>
	    <table class="form-table">
	    	<tr valign="top">
	        	<th scope="row"> <?php _e( 'Google Access Code','cf-7-to-spreadsheet' ); ?></th>
	        	<p class="description"> <?php _e( 'Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox.','cf-7-to-spreadsheet' ); ?></p>
	        	<td><input type="text" name="cf7_to_spreadsheet_google_code" required="required" value="" placeholder="<?php if ( trim( get_option('cf7_to_spreadsheet_google_token') ) !== '' ) { esc_attr_e( 'Currently Active', 'cf-7-to-spreadsheet' ); } else { esc_attr_e( ''); }?>"/>
	        		<a  href="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=448551536053-e36uicg9npg51m0e89kb51i37b6741fq.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&state&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F" target="_blank" ><?php _e( 'Get Code ','cf-7-to-spreadsheet' ); ?></a>
	           	</td>
	        </tr>
	    </table>
	    <?php submit_button(); ?>
	</form>
</div>
