<?php
$sites = $ui->getSitesConfig();
function cmp($a, $b)
{
    return strcmp($a["site_name"], $b["site_name"]);
}
usort($sites, "cmp");

