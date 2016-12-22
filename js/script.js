jQuery(document).ready(function(){
	jQuery("checking").append('error');
	jQuery('#setting-form').hide();
	jQuery('#cf7-toggle-button').click(function() {
		jQuery('#enable_spreadsheet').toggle();
	});
	jQuery('#google-connect').click(function() {
		jQuery('#setting-form').show();
	});
});