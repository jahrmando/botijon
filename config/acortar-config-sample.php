<?php
/*
 * you need to obtain your generic access token using bitly, register your account at https://bitly.com
 * https://bitly.com/a/oauth_apps
 * and create your Generic Access Token
 */

$config->acortar = new stdclass();
$config->acortar->type = "bitly"; // Available options are bitly and google, bitly requires access token
$config->acortar->token = "your-token-here";