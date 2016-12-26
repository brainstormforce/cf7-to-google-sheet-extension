<!-- CF7 to spreadsheet setting page html form -->
<?php $get_token = json_decode( get_option('cf7_to_spreadsheet_google_token'),true );  ?>
<div class="wrap cf7-status-check">
	<h1><?php _e( 'CF7 to Spreadsheet Settings','cf-7-to-spreadsheet' ); ?></h1> </br>
	<form  action="options.php" method="post" class="container">
		<?php settings_fields( 'cf7_to_spreadsheet_plugin_setting' ); ?>
		<?php do_settings_sections( 'cf7_to_spreadsheet_plugin_setting' ); ?>
		<?php 
		if ( empty( $get_token['access_token'] ) ) { ?>
			<h2><?php _e( 'Google Spreadsheet Account', 'cf-7-to-spreadsheet') ?> <span class='dashicons dashicons-dismiss not-activate'></span><span class='cf7-red-text'><?php _e('Not Connected', 'cf-7-to-spreadsheet')?> </span></h2> </br>
		<?php }
		else { ?>
			<h2><?php _e( 'Google Spreadsheet Account', 'cf-7-to-spreadsheet') ?> <span class='dashicons dashicons-yes activate'></span><span class='cf7-green-text'><?php _e('Connected', 'cf-7-to-spreadsheet')?> </span></h2> </br>	
		<?php } ?>
		<h4> <?php  _e( 'If you want to save data on Google Spreadsheet you would need to connect with Google Spreadsheet','cf-7-to-spreadsheet' ); ?></h4> </br>
		<p> <b><?php  _e( 'Step1:','cf-7-to-spreadsheet' )?></b><?php  _e( 'Please click on[connect with Google Spreadsheet] and copy the code','cf-7-to-spreadsheet' ); ?></p>
		<?php
		$google_url = Cf7_Google_Spreadsheet::google_connect_url();
		global  $error_message_code;
		echo $error_message_code; 
		echo '<a id="cf7-google-connect" class="button-primary"  href="'.$google_url.'" target="_blank" > ';
		if( empty( $get_token['access_token'] ) ) {
			_e( 'Connect with Google Spreadsheet','cf-7-to-spreadsheet' );
		} else { 
			_e( 'Reconnect with Google Spreadsheet','cf-7-to-spreadsheet') ;
		} ?> </a> </br> </br>
		
		<div id="cf7-setting-form">
			<p> <b><?php  _e( 'Step2:','cf-7-to-spreadsheet' )?></b><?php  _e( 'Paste access code and save settings','cf-7-to-spreadsheet' ); ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"> <?php _e( 'Google Access Code','cf-7-to-spreadsheet' ); ?></th>
					<td><input type="text" class="access-code-input-box" name="cf7_to_spreadsheet_google_code" required="required" autocomplete="off" placeholder="Please enter access code" /></td>
				</tr>
			</table>
			<?php submit_button(); ?> 
		</div>
	</form>
</div>
