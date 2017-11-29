<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
    {preventCsrf}

    <div class="wrap-list">
        <div class="wrap-group">
            <div class="wrap-group-heading">
                <h4>{lang key='options'}</h4>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-from_name">{lang key='from_name'}</label>
                <div class="col col-lg-4">
                    <input type="text" name="from_name" value="{$item.from_name|escape:'html'}" id="input-from_name">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-from_mail">{lang key='from_mail'}</label>
                <div class="col col-lg-4">
                    <input type="text" name="from_mail" value="{$item.from_mail|escape:'html'}" id="input-from_mail">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label">{lang key='recipients'}</label>
                <div class="col col-lg-4">
                    <div class="box-simple fieldset">
                        {foreach $usergroups as $id => $title}
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="groups[]" value="{$id}"{if (isset($smarty.post.groups) && in_array($id, $smarty.post.groups)) || !$smarty.post} checked{/if}>
                                    {lang key="usergroup_{$title}"}
                                </label>
                            </div>
                        {/foreach}
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="subscribers" value="1" checked> {lang key='subscribers'}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label">{lang key='status'}</label>
                <div class="col col-lg-4">
                    <div class="box-simple fieldset">
                        {foreach $statuses as $status}
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="st[]" value="{$status}"{if (isset($smarty.post.st) && in_array($status, $smarty.post.st)) || !$smarty.post} checked{/if}>
                                    {lang key=$status}
                                </label>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label">{lang key='type'}</label>
                <div class="col col-lg-4">
                    <label class="radio">
                        <input type="radio" name="type" value="text"{if 'text' == $item.type} checked{/if}> {lang key='text'}
                    </label>
                    <label class="radio">
                        <input type="radio" name="type" value="html"{if 'text' != $item.type} checked{/if}> {lang key='html'}
                    </label>
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-subj">{lang key='subject'}</label>
                <div class="col col-lg-4">
                    <input type="text" name="subj" id="input-subj" value="{$item.subj|escape:'html'}">
                </div>
            </div>

            <div class="row">
                <label class="col col-lg-2 control-label" for="input-body">{lang key='body'}</label>
                <div class="col col-lg-9" id="email_body">
                    <textarea name="body" id="text_body" cols="20" rows="10" style="display:none;">{$item.body|escape:'html'}</textarea>
                    {ia_wysiwyg name='html_body' value=$item.html_body}
                </div>
            </div>
        </div>

        <div class="form-actions inline">
            <button type="submit" name="save" class="btn btn-primary">{lang key='add'}</button>
        </div>
    </div>
</form>
{ia_add_media files='js:_IA_URL_modules/newsletters/js/admin/mailer'}
