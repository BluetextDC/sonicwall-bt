// BEGIN SHOW SEARCH FORM
jQuery('.morph-search-button').on('click', function(e) {
'use strict';
	e.preventDefault();
		if(jQuery('.morph-search-wrapper').hasClass('morph-search-wrapper-active'))
		{
			/* hide search field close button */
			jQuery('.morph-search-close-wrapper').removeClass('morph-search-close-wrapper-active');
			/* hide search field */
			jQuery('.morph-search-wrapper').removeClass('morph-search-wrapper-active');
			jQuery('.morph-search-wrapper #searchform #s').blur();
			/* show search button */
			jQuery('.morph-search-button').removeClass('morph-search-button-hidden');
		} else {
			/* show search field close button */
			jQuery('.morph-search-close-wrapper').addClass('morph-search-close-wrapper-active');
			/* show search field */
			jQuery('.morph-search-wrapper').addClass('morph-search-wrapper-active');
			/* focus search field */
			jQuery('.morph-search-wrapper #searchform #s').focus();
			/* hide search button */
			jQuery('.morph-search-button').addClass('morph-search-button-hidden');
			
            /* hide menu button label */
			jQuery('.morph-menu-button-label').addClass('morph-menu-button-label-hide');
			/* hide secondary menu */
			jQuery('.morph-secondary-menu-wrapper').removeClass('morph-secondary-menu-wrapper-active');
			/* secondary menu button inactive state */
			jQuery('.morph-secondary-menu-button').removeClass('morph-secondary-menu-button-active');
		}
});
// END SHOW SEARCH FORM

// BEGIN HIDE SEARCH FORM
jQuery('.morph-search-close-wrapper').on('click', function(e) {
'use strict';
	e.preventDefault();
		/* hide search field close button */
		jQuery('.morph-search-close-wrapper').removeClass('morph-search-close-wrapper-active');
		/* hide search field */
		jQuery('.morph-search-wrapper').removeClass('morph-search-wrapper-active');
		jQuery('.morph-search-wrapper #searchform #s').blur();
		/* show search button */
		jQuery('.morph-search-button').removeClass('morph-search-button-hidden');
        /* show menu button label */
		jQuery('.morph-menu-button-label').removeClass('morph-menu-button-label-hide');
});
// END HIDE SEARCH FORM