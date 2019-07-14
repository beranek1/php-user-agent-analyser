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
    $trident = false;
    if(preg_match_all("/\w+\/\d[\d.]*/", $user_agent, $matches)) {
        $browser = preg_split("/\//",$matches[0][array_key_last($matches[0])]);
        if($webkit) {
            if(preg_match("/safari/i", $browser[0]) && preg_match("/chrome/i", $user_agent)) {
                $browser = preg_split("/\//",$matches[0][2]);
            }
        } else if(preg_match("/trident/i", $browser[0]) && !$gecko) {
            $trident = true;
        }
        $result["browser"]["name"] = $browser[0];
        $result["browser"]["version"] = $browser[1];
    }
    if(preg_match("/\([A-Za-z0-9_. ;:\/]*\)/", $user_agent, $match)) {
        $platform = preg_replace("/\(/", "", $match[0]);
        $platform = preg_replace("/\)/", "", $platform);
        $platforms = preg_split("/; /", $platform);
        if($trident) {
            $browser = preg_split("/ /",$platforms[1]);
            if(preg_match("/msie/i", $browser[0])) {
                $result["browser"]["name"] = $browser[0];
                $result["browser"]["version"] = $browser[1];
            } else {
                $result["browser"]["name"] = "msie";
                $version = preg_split("/:/", $platforms[array_key_last($platforms)]);
                $result["browser"]["version"] = $version[1];
            }
        }
    }
    return $result;
}