<?

    $script = "scriptsJS/confirmUser.js";
    $output = shell_exec("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --proxy=p-uk1.biscience.com:3128 --proxy-auth=andreya@ufins.com:ndye8764 scriptsJS/confirmUser.js https://www.freesexmatch.com/site/autologin/key/e634d4a61e9ffa4f2c399b01087e8ed0");

	var_dump($output);
/*

libs/PhantomJS/phantomjs --proxy=p-uk1.biscience.com:3128 --proxy-auth=andreya@ufins.com:ndye8764 scriptsJS/registerUser.js https://www.playcougar.com 'ide777spainbn3@gmail.com' null null null


libs/PhantomJS/phantomjs --proxy=p-uk1.biscience.com:3128 --proxy-auth=andreya@ufins.com:ndye8764 scriptsJS/registerUser.js https://www.playcougar.com ide777spainbn3@gmail.com Firefox male 45

$email, 

$site, 

$gender = 'male', 

$age = 21, 

$user_agent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0", 


--proxy-type=[http|socks5|none] specifies the type of the proxy server (default is http).

*/
?>
