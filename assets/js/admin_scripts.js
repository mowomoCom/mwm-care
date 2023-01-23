jQuery(window).load(function () {

	/**
	 * We change the tab to the one before reloading the page.
	 */
	if (window.location.hash) {
		jQuery('.mwm_admin_page .nav-tab[href="' + window.location.hash + '"]').trigger('click');
	}

	/**
	 * Regenerate maintenance link
	 */
	jQuery(document.body).on('click', '#maintenance_regenerate_link', function () {
		var button = jQuery(this);

		jQuery.ajax({
			url: mwmpc_vars.ajaxurl,
			type: "post",
			data: {
				action: "mwmpc_maintenance_regenerate_link",
			},
			beforeSend: function () {
				button.text(mwmpc_vars.regenerating_text_loading);
				button.prop('disabled', true);
			},
			success: function (response) {
				if (response.result) {
					jQuery("#_main_acce_url").val(response.result);
				} else {
					alert(mwmpc_vars.error_message);
				}
			},
			complete: function () {
				button.text(mwmpc_vars.regenerating_text);
				button.prop('disabled', false);
			}
		});
	});

	/**
	 * Copy value to clipboard
	 */
	jQuery(document.body).on('click', '.mwm_copy_value', function () {
		var tooltip = jQuery(this).find('.mwm_copy_value__tooltip');
		var value = jQuery(this).find('.mwm_copy_value__value').val();
		navigator.clipboard.writeText(value);
		tooltip.text(mwmpc_vars.copied_text);
	});


});
