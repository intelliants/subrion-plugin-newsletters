$(function()
{
	$('input[name="type"]').on('click', function()
	{
		var type = $(this).val();
		if ('text' == type)
		{
			$('#text_body').show();
			$('#cke_html_body').hide();
		}
		else
		{
			$('#text_body').hide();
			$('#cke_html_body').show();
		}
	});
});
