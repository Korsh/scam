<script type="text/javascript" src="/js/jquery.user.js"></script>         
<script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script> 
<script type="text/javascript" src="/js/monitor.js"></script> 
<script type="text/javascript" src="/js/chosen_v1.2.0/chosen.jquery.js"></script>
<link rel="stylesheet" href="/js/chosen_v1.2.0/chosen.css">
<link rel="stylesheet" type="text/css" media="all" href="/css/styles.css" />
<script>
{literal}
$(function() {


    $("#country").chosen(
        {no_results_text: "Oops, nothing found!",width: "5%"}
    ); 

    var config = {
        '.chosen-select'           : {},
    }

    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    $("#site").chosen({
        search_contains: true,
        allow_single_deselect: true,
        no_results_text: "Oops, nothing found!"
    });
    $("#platform").chosen({
        no_results_text: "Oops, nothing found!"
    });
    $("#gender").chosen({
        no_results_text: "Oops, nothing found!"
    });    
});
{/literal}
</script>

<select class="chosen-select" data-placeholder="Site..." id="site">
    <option></option>{foreach name=sites_list item=site from=$sites_conf}<option value="{$site.domain}">{$site.domain}({$site.locale})</option>{/foreach}
</select>
<select class="chosen-select" data-placeholder="Country..." id="country">
    <option></option>{foreach name=proxy_list item=proxies key=proxy from=$proxy_conf}<option value="{$proxy}">{$proxy}</option>{/foreach}
</select>

<select class="chosen-select" data-placeholder="Platform..." id="platform">
<option></option>
<option value="8">androidApp</option>
<option value="6">FB widget @facebook.</option>
<option value="4">fbApp</option>
<option value="5">fbWidget</option>
<option value="9">fbWidgetMobile</option>
<option value="7">iosApp</option>
<option value="3">mobApp</option>
<option value="2">mobSite</option>
<option value="0">undefined</option>
<option value="1">webSite</option>
</select>
<select class="chosen-select" data-placeholder="Gender..." id="gender">
    <option></option>
    <option value="male">male</option>
    <option value="female">female</option>
</select>
<input type="date" id="dateFrom" value="{$smarty.now|date_format:"%Y-%m-%d"}">
<input type="date" id="dateTo" value="{$smarty.now|date_format:"%Y-%m-%d"}">
<input type="button" id="submit" value="Get">
{foreach from=$userActivity item=user key=userKey}
<table id="monitor_table" width="1000px">
    <tr>
        <th width="15%">
            {$userKey}
        </th>
        <th>
            {$user.site}
        </th>
        <th>
            {$user.source}
        </th>
        <th>
            {$user.actionWay}
        </th>
        <th>
            {$user.platform}
        </th>
        <th>
            {$user.confirm}
        </th>
        <th>
            {$user.scammer}
        </th>
        <th>
            {$user.paid}
        </th>
        <th>
            {$user.gender}
        </th>
        <th>
            {$user.year}
        </th>
        <th>
            {$user.country}
        </th>
        <th>
            {$user.language}
        </th>
    </tr>
    <tr>
        <th>
            screenname
        </th>
        <th colspan="5">
            group
        </th>
        <th colspan="3">
            receive
        </th>
        <th>
            country
        </th>
        <th>
            year
        </th>
        <th>
            status
        </th>
    </tr>
    {foreach from=$user.messages item=message key=curr}
    <tr>
        {if !$message}
            <td colspan="12">
                empty
            </td>
        {else}
            <td>
                {$message.screenname}
            </td>
            <td colspan="5">
                {$message.group}({$message.groupId})
            </td>
            <td colspan="3">
                {$message.receiveTime}
            </td>
            <td>
                {$message.country}
            </td>
            <td>
                {$message.year}
            </td>
            <td>
                {$message.status}
            </td>
        {/if}
    </tr>
    {/foreach}
</table>
<br>
{/foreach}
