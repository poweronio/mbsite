jQuery(document).ready(function ($) {
	$('#wpfepp-dismiss-nag').click(function (e) {
		e.preventDefault();
		$(this).closest('.updated').hide();
		$.ajax({ type:'POST', url: ajaxurl, data: { action: 'wpfepp_dismiss_nag' } });
	});
});