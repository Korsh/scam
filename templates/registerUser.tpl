  <script type="text/javascript" src="/js/jquery.user.js"></script>              
  <script type="text/javascript" src="/js/jquery-ui.custom.min.js"></script>      
  <script type="text/javascript" src="/js/register.js"></script>              
  <link rel="stylesheet" type="text/css" media="all" href="/css/styles.css" />
  <link rel="stylesheet" type="text/css" media="all" href="/css/jquery-ui.custom.min.css" />
Get activity: 
<input type="text" id="activity_user_id" class="clear">
<button name="get_activity" id="get_activity">Get</button><br><div id="activity_result"></div><hr>
E-mail: 
<input class="clear" id="email"type="email" value="e-mail">

Password: 
<input class="clear" id="password" type="text" size="5" value="password">

Age: 
<input class="clear small2" id="age" type="number" maxlength="2" size="1" value="21">

Gender: 
<select id="gender">
	<option value="male">male</option>
	<option value="female">female</option>
</select>

Country: 
<select id="country">
	{foreach name=proxy_list item=proxies key=proxy from=$proxy_conf}<option value="{$proxy}">{$proxy}</option>{/foreach}
</select>

Site: 
<select id="site">
	{foreach name=sites_list item=site from=$sites_conf}{if $site.company_name == "Alcuda Limited" or $site.company_name == "Cisca Services Ltd" or $site.company_name == "Cisca Services Limited" or $site.company_name == "Ldate Ltd"}<option value="{$site.domain}">{$site.domain}</option>{/if}{/foreach}
</select>

Device: 
<select id="device">
	{foreach name=user_agents_list item=user_agents key=device from=$user_agents_conf}
			<option value="{$device}">{$device}</option>
	{/foreach}
</select>

Count: 
<input class="clear small2" id="count" type="number" maxlength="2" size="2" value="1">

Referer:
<input type="checkbox" id="referer_check">
<input type="url" id="referer">
<button name="add_users" id="add_users">Add</button>



