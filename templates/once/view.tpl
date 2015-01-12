Once task #{$task_id}
<div class="task-container">
    <ul class="task-choices">
    {foreach item=task from=$task_info key=task_id}
            <li class="tasks-choice">
                {$task_id|date_format:"%D"}, {$task_id|date_format:"%H:%S"}<hr>
                {foreach item=site key=site_name from=$task name=site}<a href="/once/view/{$task_id}?site={$site_name}">{$site_name}</a>({foreach from=$site item=country name=country}<a href="/once/view/{$task_id}?location={$country}">{$country}</a>{if !$smarty.foreach.country.last}, {/if}{/foreach}){if !$smarty.foreach.site.last}<br>{/if}{/foreach}</li>
    {/foreach}
    </ul>
</div>
<div><a class="link" id="update_activity">Update</a></div>
<div><a class="link" id="sync_users">Sync</a></div>

{include file="user_info.tpl"}
<table id="data-sheet">
    <tr>
        <th>User info</th>
        <th>Chats info</th>
    </tr>

{foreach item=user from=$users_info name=users_info}
    <tr>
        <td>{$user.site}
            <span class="unselectable"><a target="_blank" href="https://{$user.site}.com/site/autologin/key/{$user.key}">{$user.site}</a>({$user.country})<br>
            {$user.mail}<br>
            {$user.gender}<br>
            {$user.register}<br>
            {$user.birthday}<br>
            searchable: {$user.searchable}<br></span>
            <a class="link get_user_info">{$user.id}</a><br>
            <span class="unselectable"><a target="_blank" href="https://my.ufins.com/user/find?user_id={$user.id}">admin</a><br>
            {$user.platform}
            <br>
            
            Message: {if $user.chats|@sizeof == 1 && $user.chats[0].message == 'empty'}0{else}{$user.chats|@sizeof}{/if} / {$user.chatsCount}</span><div>
            <img src="http://maps.googleapis.com/maps/api/staticmap?center={$user.ll}&zoom=2&size=200x200&sensor=true&markers=color:blue%7C{$user.ll}"></div>
        </td>
        <td>
            <table id="chat_info">
            {foreach item=chat from=$user.chats}
            
                {if $chat.message != 'empty' && $chat.user != 'empty'}
                <tr {if $chat.user.99}
                        class="green"
                {else}
                        class="yellow"
                {/if}>
                    <!--<td>email: {$chat.user.mail}</td>-->
                    {if $chat.user.screenname == '' && $chat.user.ll == 'no data' && $chat.user.distance_error == 'true'}
                    <td class="red" colspan="3">
                     No:{$chat.user.id}
                    </td>
                    {else}
                    <td><span class="unselectable"><b>{$chat.user.screenname}</b>, ({$chat.user.age})<br><span  class="unselectable"><b>{$chat.message.time}</b></span><br></span><a {if $chat.user.99 == 1 && ($chat.user.distance_error || $chat.message.message_error)}class="selectable red"{else}class="unselectable"{/if} target="_blank" href="https://my.ufins.com/user/edit?user_id={$chat.user.id}">{$chat.user.id}</a><br></td>
                    <!--<td>is 99: {$chat.user.99}</td>-->
                    <!--<td {if $chat.user.99 == 1 && $chat.user.distance_error == 'true'}
                        class="red"
                    {/if}><span  class="unselectable">dist: {$chat.user.distance} ({$chat.user.ll})</span>
                    </td>
                    {if $chat.user.99 == 1 && $chat.user.address_error}
                    <td class="red">
                    [{$chat.user.address}]{$chat.user.ll} - {$chat.user.address_ll} = {$chat.user.address_shift}
                    </td>
                    {/if}-->
                    <!--
                    {if $chat.user.address_error == 3}
                    <td>
                        address: {$chat.user.address}<br>
                        ll: {$chat.user.address_ll}<br>
                        <span {if $chat.user.address_error}class="unselectable red"{/if}>address shift: {$chat.user.address_shift}</span><br>
                    </td>
                    {/if}-->
                    {if $chat.user.distance_error}
                    <td>
                        <!--user ll: {$user.ll}<br>
                        sender ll: {$chat.user.ll}<br>-->
                        <span {if $chat.user.distance_error}class="selectable red"{/if}>distance: </span><span class="unselectable">{$chat.user.distance}</span><br>
                    </td>
                    {/if}
                    {if $chat.message.message_error}
                    <td><span {if $chat.user.99 == 1 && $chat.message.message_error == 'true'}
                        class="selectable red"
                    {/if}><i>"{$chat.message.text}"</i></span></td>{/if}
                    {/if}
                </tr>
                {else}
                <tr class="red">
                    <td>null</td>
                </tr>
                {/if}
            {/foreach}
            </table>
        </td>
    </tr>
{/foreach}
    
</table>

