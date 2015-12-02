<div class="text-center">
	{if $core.config.newsletters_block_fullname}
		<input type="text" id="newsletters-name" placeholder="{lang key='enter_fullname'}" class="input-block-level">
	{/if}
	<input type="text" id="newsletters-email" placeholder="{lang key='enter_email'}" class="input-block-level">
	<div class="alert hide" id="newsletters-msg">
		<button type="button" class="close">×</button>
		<span class="msg"></span>
	</div>
	<button type="button" class="btn btn-block" id="newsletters-subscribe"><i class="icon icon-check"></i> {lang key="subscribe"}</button>
</div>
{ia_print_js files='_IA_URL_plugins/newsletters/js/frontend/subscription-form'}
