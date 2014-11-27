<script type="text/javascript" src="/js/jquery.user.js"></script>         
<script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>    
<script type="text/javascript" src="/js/repeat.js"></script>   
<link rel="stylesheet" type="text/css" media="all" href="/css/styles.css" />
<a href="/repeat/add">Add new task</a> |
<a href="/repeat/active">Active tasks</a>
<a href="/repeat/history">Inactive tasks</a>
<hr>
{if $repeat == "active"}
	{include file="repeat/active.tpl"}
{elseif $repeat == "edit"}
	{include file="repeat/edit.tpl"}
{elseif $repeat == "add"}
	{include file="repeat/add.tpl"}
{elseif $repeat == "view"}
	{include file="repeat/view.tpl"}
{else}
	{include file="repeat/history.tpl"}
{/if}
