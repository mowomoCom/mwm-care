jQuery(window).load(function () {

	/**
	 * We change the tab to the one before reloading the page.
	 */
	if (window.location.hash) {
		jQuery('.mwm_admin_page .nav-tab[href="' + window.location.hash + '"]').trigger('click');
	}


});
