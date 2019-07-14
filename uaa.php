<?php
/*
#-----------------------------------------
| UUA: User Agent Analyser
| https://github.com/beranek1/UAA
#-----------------------------------------
| made by beranek1
| https://github.com/beranek1
#-----------------------------------------
*/

function analyse_user_agent($user_agent) {
    $result = array("browser" => array("name" => null, "version" => null));
    $gecko = false;
    if(preg_match("/Mozilla\/\d[\d.]* \([A-Za-z0-9_. ;:]*\) Gecko\/\d+/i", $user_agent)) {
        $gecko = true;
    }
    $webkit = false;
    if(preg_match("/Mozilla\/\d.\d \([A-Za-z0-9_. ;:]*\) AppleWebKit\/\d[\d.]* \(KHTML, like Gecko\)/i", $user_agent)) {
        $webkit = true;
    }
    if(preg_match_all("/\w+\/\d[\d.]*/", $user_agent, $matches)) {
        $browser = preg_split("/\//",$matches[0][array_key_last($matches[0])]);
        if($webkit) {
            if(preg_match("/safari/i", $browser[0]) && preg_match("/chrome/i", $user_agent)) {
                $browser = preg_split("/\//",$matches[0][2]);
            }
        }
        $result["browser"]["name"] = $browser[0];
        $result["browser"]["version"] = $browser[1];
    }
    return $result;
}