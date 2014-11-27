<script type="text/javascript" src="/js/jquery.user.js"></script>         
<script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>    
<script type="text/javascript" src="/js/once.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="/css/styles.css" />
<a href="/once/add">Add new task</a> |
<a href="/once/history">Tasks</a>
<hr>
{if $once == "add"}
	{include file="once/add.tpl"}
{elseif $once == "view"}
	{include file="once/view.tpl"}
{else}
	{include file="once/history.tpl"}
{/if}
{include file="loading.tpl"}
