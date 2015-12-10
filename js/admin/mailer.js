$(function()
{
	$('input[name="type"]').on('click', function()
	{
		if ('text' == $(this).val())
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
