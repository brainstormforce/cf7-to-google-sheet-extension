jQuery(document).ready(function(){
	jQuery('#cgs-setting-form').hide();
	jQuery('#cgs-toggle-button').click(function() {
		jQuery('#cgs-enable_spreadsheet').toggle();
	});
	jQuery('#cgs-google-connect').click(function() {
		jQuery('#cgs-setting-form').show();
	});
});