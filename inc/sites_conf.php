<?php

$sites = $ui->getSitesConfig();
function cmp($a, $b)
{
    return strcmp($a["site_name"], $b["site_name"]);
}
usort($sites, "cmp");
$sites['locales'] = array(
    "AUS",
    "BRA",
    "CAN",
    "DEU",
    "DNK",
    "ESP",
    "FIN",
    "FRA",
    "GBR",
    "IRL",
    "ITA",
    "NOR",
    "NZL",
    "SWE",
    "USA",
    "BEL",
    "CZE"
);
function findDictionaryIdInSitesConfig($siteToFind, $siteConfig)
{
    $dictionaryId = false;
    foreach($siteConfig as $site) {
        if(($site['site_name'] == $siteToFind)
            || ($site['domain'] == $siteToFind)
            || ($site['live'] == $siteToFind)) {
            $dictionaryId[] = $site['dictionaryId'];
        } else {
            continue;
        }
    }
    return $dictionaryId;
}
