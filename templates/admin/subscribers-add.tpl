<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='general'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-email">{lang key='email'} {lang key='field_required'}</label>
				<div class="col col-lg-4">
					<input type="text" name="email" value="{$item.email|escape:'html'}" id="input-email">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-fullname">{lang key='fullname'}</label>
				<div class="col col-lg-4">
					<input type="text" name="fullname" value="{$item.fullname|escape:'html'}" id="input-fullname">
				</div>
			</div>
		</div>

		{include file='fields-system.tpl' datetime=true}
    </div>
</form>