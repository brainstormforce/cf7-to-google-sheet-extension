<!-- Contact form7 editor panel tab html form -->
<?php 
	$cgs_form_id   = sanitize_text_field( $_GET['post'] );
	$spreadsheet_data = get_post_meta( $cgs_form_id, 'cf7_to_spreadsheet_data' ); 
?>
<div class="wrap cgs-fields">
	<form  method="post" class="container">
		<table class="form-table">
			<h2><?php _e( 'CF7 to Spreadsheet','cf-7-to-spreadsheet' ); ?></h2>
			<tr valign="top">
				<th scope="row"><?php _e( 'Send data to Spreadsheet','cf-7-to-spreadsheet' )?> </th>
				<td>
					<div class="cgs-toggle-button">
						<input type="checkbox" name="cf7-sheet[checked]" class="cgs-toggle-button-checkbox" id="cgs-toggle-button" value="1" <?php checked( isset($spreadsheet_data[0]['checked']), 1 ); ?>/>
						<label class="cgs-toggle-button-label" for="cgs-toggle-button">
							<span class="cgs-toggle-button-inner"></span>
							<span class="cgs-toggle-button-switch"></span>
						</label>
					</div>
				</td>
			</tr>
		</table>
		<div id="cgs-enable_spreadsheet" <?php if ( !isset( $spreadsheet_data[0]['checked'] ) ) echo 'style="display:none;"'; ?> >
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Google Spreadsheet Name','cf-7-to-spreadsheet' ); ?></th>
					<td> <input type="text" name="cf7-sheet[sheet-name]" class="large-text code" value="<?php echo ( isset ( $spreadsheet_data[0]['sheet-name'] ) ) ? esc_attr( $spreadsheet_data[0]['sheet-name'] ) : ''; ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Google Spreadsheet Tab Name','cf-7-to-spreadsheet' ); ?></th>
				<td>
					<input type="text" name="cf7-sheet[sheet-tab-name]" class="large-text code" value="<?php echo ( isset ( $spreadsheet_data[0]['sheet-tab-name'] ) ) ? esc_attr( $spreadsheet_data[0]['sheet-tab-name'] ) : ''; ?>" />
				</td>
				</tr>
			</table>
			<p> <a href="<?php  echo esc_url( CGS_DOC_URL ) ?>" target='_blank'> <?php _e('How to configure your Spreadsheet?','cf-7-to-spreadsheet' )?> </a> </p>

		</div>
	</form>
</div>

