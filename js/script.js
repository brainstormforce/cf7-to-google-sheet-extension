jQuery(document).ready(function(){
	jQuery('#setting-form').hide();
	jQuery('#mytoggle-button').click(function() {
		jQuery('#enable_spreadsheet').toggle();
	});
	jQuery('#google-connect').click(function() {
		jQuery('#setting-form').show();
	});
});