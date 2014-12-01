Once tasks history
<div class="clearer"></div>
<div class="task-container">
	<ul class="task-choices">
	{foreach item=task from=$tasks_list key=task_id}
		<a href="/once/view/{$task_id}">
			<li class="tasks-choice">
				{$task_id|date_format:"%D"}, {$task_id|date_format:"%H:%S"}<hr>
				{foreach item=site key=site_name from=$task name=site}{$site_name}({foreach from=$site item=country name=country}{$country}{if !$smarty.foreach.country.last}, {/if}{/foreach}){if !$smarty.foreach.site.last}<br>{/if}{/foreach}</li>
			</a>&nbsp;
	{/foreach}
	</ul>
</div>
