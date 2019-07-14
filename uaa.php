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
    $result = array("browser" => array("name" => null, "version" => null), "os" => array("name" => null, "version" => null), "device" => array("name" => null));
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
    if(preg_match("/\([A-Za-z0-9_.\- ;:\/]*\)/", $user_agent, $match)) {
        $platform = preg_replace("/\(/", "", $match[0]);
        $platform = preg_replace("/\)/", "", $platform);
        $platforms = preg_split("/; /", $platform);
        $os = null;
        $osv = null;
        if($trident) {
            $browser = preg_split("/ /",$platforms[1]);
            if(preg_match("/msie/i", $browser[0])) {
                $result["browser"]["name"] = $browser[0];
                $result["browser"]["version"] = $browser[1];
                $os = preg_split("/ \d/", preg_replace("/ nt/i", "",$platforms[2]));
                $osv = preg_split("/ /",$platforms[2]);
            } else {
                $result["browser"]["name"] = "msie";
                $version = preg_split("/:/", $platforms[array_key_last($platforms)]);
                $result["browser"]["version"] = $version[1];
            }
        }
        if(preg_match("/windows/i", $platforms[0])) {
            $os = preg_split("/ \d/", preg_replace("/ nt/i", "",$platforms[0]));
            $osv = preg_split("/ /",$platforms[0]);
            $result["device"]["name"] = "pc";
            if(preg_match("/phone/i", $os[0])) {
                $result["device"]["name"] = $platforms[array_key_last($platforms)-1]." ".$platforms[array_key_last($platforms)];
            }
            if(isset($platforms[3]) && preg_match("/xbox/i", $platforms[3])) {
                $result["device"]["name"] = $platforms[3];
                if(isset($platforms[4])) {
                    $result["device"]["name"] = $platforms[4];
                }
            }
        } else if(preg_match("/linux/i", $platforms[0])) {
            $os = preg_split("/ \d/",$platforms[1]);
            $osv = preg_split("/ /",$platforms[1]);
            if(preg_match("/android/i", $os[0]) && preg_match("/build/i", $platforms[2])) {
                $device = preg_split("/ build/i", $platforms[2]);
                $result["device"]["name"] = $device[0];
            }
        } else if(preg_match("/cros/i", $platforms[1])) {
            $os = preg_split("/ /",$platforms[1]);
            $result["device"]["name"] = "chromebook";
        } else if(preg_match("/macintosh/i", $platforms[0])) {
            $os = preg_split("/ \d/",preg_replace("/intel /i", "", $platforms[1]));
            $osv = preg_split("/ /",$platforms[1]);
            $result["device"]["name"] = "mac";
        } else if(preg_match("/iphone/i", $platforms[0]) || preg_match("/ipad/i", $platforms[0]) || preg_match("/ipod/i", $platforms[0])) {
            $os = preg_split("/ \d/",preg_replace("/cpu /i", "", $platforms[1]));
            $osv = preg_split("/ /", preg_replace("/ like mac os x/i", "", $platforms[1]));
            $result["device"]["name"] = $platforms[0];
        }
        $result["os"]["name"] = isset($os) ? $os[0] : null;
        $result["os"]["version"] = isset($osv) ? $osv[array_key_last($osv)] : null;
    }
    return $result;
}