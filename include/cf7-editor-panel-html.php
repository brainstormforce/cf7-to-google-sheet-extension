<!-- Contact form7 editor panel tab html form -->
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
