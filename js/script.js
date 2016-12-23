jQuery(document).ready(function(){
	jQuery('#cf7-setting-form').hide();
	jQuery('#cf7-toggle-button').click(function() {
		jQuery('#cf7-enable_spreadsheet').toggle();
	});
	jQuery('#cf7-google-connect').click(function() {
		jQuery('#cf7-setting-form').show();
	});
});