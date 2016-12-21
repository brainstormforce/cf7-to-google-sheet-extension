<!-- Contact form7 editor panel tab html form -->
<?php 
	$form_id   = sanitize_text_field( $_GET['post'] );
	$form_data = get_post_meta( $form_id, 'cf7_to_spreadsheet_data' ); 
?>
<div class="wrap cf7-fields">
	<form  method="post" class="container"  >
		<table class="form-table">
			<h2><?php _e( 'CF7 to Spreadsheet','cf-7-to-spreadsheet' ); ?></h2>
				
			<tr valign="top">
                <th scope="row"><?php _e( 'Send data to Spreadsheet','cf-7-to-spreadsheet' )?> </th>
                <td>
                	<div class="toggle-button">
					    <input type="checkbox" name="cf7-sheet[checked]" class="toggle-button-checkbox" id="mytoggle-button" <?php if ( $form_data[0]['checked'] == 'on' ) echo'checked="checked"'; ?>
					    />
					    <label class="toggle-button-label" for="mytoggle-button">
					        <span class="toggle-button-inner"></span>
					        <span class="toggle-button-switch"></span>
					    </label>
					</div>
                </td>
            </tr>
        </table>
        <div id="enable_spreadsheet" <?php if ( $form_data[0]['checked'] != 'on' ) echo 'style="display:none;"'; ?> >
        <p> <a href="<?php echo esc_url('http://docs.sharkz.in/how-to-configure-your-spreadsheet-with-cf7-to-spreadsheet-plugin/') ?>" target='_blank'> <?php _e('How to configure your Spreadsheet?','cf-7-to-spreadsheet' )?> </a> </p>
        	<table class="form-table">
		    	<tr valign="top">
		        	<th scope="row"><?php _e( 'Google Spreadsheet Name','cf-7-to-spreadsheet' ); ?></th>
		           	<td> <input type="text" name="cf7-sheet[sheet-name]" class="large-text code" value="<?php echo ( isset ( $form_data[0]['sheet-name'] ) ) ? esc_attr( $form_data[0]['sheet-name'] ) : ''; ?>" /> 
		           	</td>
		        </tr>
		        <tr valign="top">
		        	<th scope="row"><?php _e( 'Google Spreadsheet Tab Name','cf-7-to-spreadsheet' ); ?></th>
		           	<td>
		           		<input type="text" name="cf7-sheet[sheet-tab-name]" class="large-text code" value="<?php echo ( isset ( $form_data[0]['sheet-tab-name'] ) ) ? esc_attr( $form_data[0]['sheet-tab-name'] ) : ''; ?>" />
		           	</td>
		        </tr>
		    </table>
		</div>
  	</form>
</div>

