<script type="text/javascript" src="/js/chosen_v1.2.0/chosen.jquery.js"></script>
<link rel="stylesheet" href="/js/chosen_v1.2.0/chosen.css">
<link rel="stylesheet" type="text/css" media="all" href="/css/styles.css" />
<link rel="stylesheet" type="text/css" media="all" href="/css/jquery-ui.custom.min.css" />
<script>
{literal}
$(function() {


    $(".country").chosen(
        {no_results_text: "Oops, nothing found!",width: "95%"}
    ); 

    var config = {
        '.chosen-select'           : {},
    }

    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    $(".site").chosen({
        search_contains: true,
        allow_single_deselect: true,
        no_results_text: "There`s no site such like this!", 
        width: "95%", 
    })
});
{/literal}
</script>
<input hidden id="task_id" value="{$smarty.now}">
<div id="task_parameters_block" >
Site: 
<select class="chosen-select" data-placeholder="Site..." id="site" multiple="multiple">
    <option></option>{foreach name=sites_list item=site from=$sites_conf}<option value="{$site.domain}">{$site.domain}({$site.locale})</option>{/foreach}
</select>

Country: 
<select class="chosen-select" data-placeholder="Country..." id="country" multiple="multiple">
    <option></option>{foreach name=proxy_list item=proxies key=proxy from=$proxy_conf}<option value="{$proxy}">{$proxy}</option>{/foreach}
</select>

Device: 
<select class="chosen-select" data-placeholder="Device..." id="device" multiple="multiple">
    {foreach name=user_agents_list item=user_agents key=device from=$user_agents_conf}
            <option value="{$device}">{$device}</option>
    {/foreach}
</select>

Gender: 
<select class="chosen-select" data-placeholder="Gender..." id="gender" multiple="multiple">
    <option value="male">male</option>
    <option value="female">female</option>
</select>
Orientation: 
<select class="chosen-select" data-placeholder="Orientation..." id="orientation" multiple="multiple">
    <option value="hetero">hetero</option>
    <option value="homo">homo</option>
</select>
E-mail: 
<input class="clear" id="email"type="email" value="e-mail">

Age: 
<input class="clear small2" id="age" type="number" maxlength="2" size="1" value="21">





Count: 
<input class="clear small2" id="count" type="number" maxlength="2" size="2" value="1">

Referer:
<input type="url" id="referer">
<button name="add_users" id="add_users">Add</button>
</div>
