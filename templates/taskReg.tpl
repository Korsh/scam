  <script type="text/javascript" src="/js/jquery.user.js"></script>              
  <script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>      
<!--  <script type="text/javascript" src="/js/register.js"></script>              -->

  <script type="text/javascript" src="/js/task.js"></script>
  <script type="text/javascript" src="/js/popup.js"></script>
  <link rel="stylesheet" type="text/css" media="all" href="/css/styles.css" />
  <link rel="stylesheet" type="text/css" media="all" href="/css/jquery-ui.custom.min.css" />
<!--Get activity: 
<input type="text" id="activity_user_id" class="clear">
<button name="get_activity" id="get_activity">Get</button><br><div id="activity_result"></div><hr>-->
{if $smarty.get.task_id}
<div class="task-container">
	<ul class="task-choices">
	{foreach item=task from=$tasks_list key=task_id}
		<a href="/task?task_id={$task_id}">
			<li class="tasks-choice">
				{$task_id|date_format:"%D"}, {$task_id|date_format:"%H:%S"}<hr>
				{foreach item=site key=site_name from=$task name=site}{$site_name}({foreach from=$site item=country name=country}{$country}{if !$smarty.foreach.country.last}, {/if}{/foreach}){if !$smarty.foreach.site.last}<br>{/if}{/foreach}</li>
			</a>&nbsp;
	{/foreach}
	</ul>
</div>
<br><a href="/task">New task</a>{else}
<a href="/task?task_id=1">List of tasks</a>
<input hidden id="task_id" value={$smarty.now}>

{include file="task_parameters.tpl"}<br>
<table class="main_table" style="display:none;">

	<tr>
		<th>
			email
		</th>
		<th>
			password
		</th>
		<th>
			age
		</th>
		<th>
			gender
		</th>
		<th>
			country
		</th>
		<th>
			site
		</th>
		<th>
			device
		</th>
	</tr>
{/if}

{if $smarty.get.task_id}
<!--{if $users_info}
{/if}-->
{include file="user_info.tpl"}
<table>

{foreach item=user from=$users_info name=users_info}
		
	{if $smarty.foreach.users_info.first}
		<tr>
		{foreach from=$user item=value key=th}
			<th>{$th}</th>			
		{/foreach}
		</tr>
	{/if}
	{foreach from=$user item=value key=th}
		
		{if $th == 'chats'}
		<td>
			<table>
			{foreach item=chat from=$user.chats}
				<tr>
				<td>{$chat.message}</td>
				<td>{$chat.user}</td>
				</tr>
			{/foreach}
			</table>
		</td>
		{else}	
			<td>{if $th == 'id'}<a class="link get_user_info">{/if}{$value}{if $th == 'id'}</a>{/if}</td>
		{/if}	
	{/foreach}
		<!--<td>{$value}</td>-->
	<!--<table>
		{foreach item=chat from=$user.chats}
			<tr>
			<td>{$chat.message}</td>
			<td>{$chat.user}</td>
			</tr>
		{/foreach}
		</table>--></td>
	
	</tr>
{/foreach}
	
</table>
{/if}
