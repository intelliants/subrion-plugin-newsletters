$(function()
{
	$('#newsletters-subscribe').click(function(){
		var fullname_input = $('#newsletters-name');
		var email_input = $('#newsletters-email');

		if (email_input.val() == '')
		{
			$('#newsletters-msg')
				.toggleClass('alert-error')
				.slideDown(100);

			$('#newsletters-msg .msg').html(_t('empty_email_input'));
		}
		else
		{
			console.log(fullname_input.val());
			$.ajax({
				url: intelli.config.ia_url + 'newsletters/read.json',
				data: { subscriber_email: email_input.val(), subscriber_fullname: fullname_input.val()},
				dataType: 'json',
				async: false,
				success: function (response) {
					$('#newsletters-msg')
						.toggleClass('alert-error', response.error)
						.toggleClass('alert-success', !response.error)
						.slideDown(100);

					$('#newsletters-msg .msg').html(response.message);
				}
			});
		}
	});

	$('#newsletters-msg .close').click(function(){
		$('#newsletters-msg').slideUp(100);
	});
});
