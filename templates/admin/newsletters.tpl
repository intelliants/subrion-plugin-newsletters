{if $queue}
    <div class="widget widget-default">
        <div class="widget-content">
            <table class="table table-hover" id="queue">
                <thead>
                <tr>
                    <th width="70%">{lang key='subject'}</th>
                    <th width="10%">{lang key='status'}</th>
                    <th>{lang key='recipients'}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach $queue as $entry}
                    <tr id="group_{$entry.id}">
                        <td>{if $entry.subj}{$entry.subj}{else}---{/if}</td>
                        <td>{if $entry.active}{lang key='active'}{else}{lang key='pending'}{/if}</td>
                        <td>{$entry.total}</td>
                        <td class="text-right">
                            <a class="btn{if $entry.active} btn-warning{else} btn-success{/if} btn-sm" href="{$smarty.const.IA_ADMIN_URL}newsletters/toggle/{$entry.id}/" rel="pause" title="{if $entry.active}{lang key='pause'}{else}{lang key='start'}{/if}">
                                {if $entry.active}
                                    <i class="i-minus-alt"></i>
                                {else}
                                    <i class="i-ok-sign"></i>
                                {/if}
                            </a>
                            <a class="btn btn-danger btn-sm" href="{$smarty.const.IA_ADMIN_URL}newsletters/delete/{$entry.id}/" rel="remove" title="{lang key='delete'}">
                                <i class="i-remove"></i>
                            </a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{else}
    <div class="widget widget-default">
        <div class="widget-content">
            {lang key='queue_empty'}
        </div>
    </div>
{/if}
