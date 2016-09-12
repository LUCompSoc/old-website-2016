$(function()
{
	$('input[id=auto-irc]').change(function()
	{
		console.log()
		$(this).is(':checked') ? $('#irc-password').show() : $('#irc-password').hide();
	});
});