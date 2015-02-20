<table>
    <tr>
        <th>
            Site
        </th>
        <th>AUS</th>
        <th>BRA</th>
        <th>CAN</th>
        <th>DEU</th>
        <th>DNK</th>
        <th>ESP</th>
        <th>FIN</th>
        <th>FRA</th>
        <th>GBR</th>
        <th>IRL</th>
        <th>ITA</th>
        <th>NOR</th>
        <th>NZL</th>
        <th>SWE</th>
        <th>USA</th>
        <th>BEL</th>
        <th>CZE</th>
    </tr>
{foreach from=$sites_conf item=site}
    <tr>
        <td>{$site.site_name}</td>
        
        {foreach from=$sites_conf.locales item=locale}
        <td>
            <input type="checkbox" class="{$site.site_name}" id="{$locale}">
        </td>
        {/foreach}
    </tr>
{/foreach}


