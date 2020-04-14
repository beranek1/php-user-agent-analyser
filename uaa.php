<?php
/*
#-----------------------------------------
| UUA: User Agent Analyser
| https://uaa.beranek.one
#-----------------------------------------
| made by beranek1
| https://github.com/beranek1
#-----------------------------------------
*/

if (!function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }
        return array_keys($array)[count($array)-1];
    }
}

function analyse_user_agent($user_agent) {
    $result = array();
    $gecko = preg_match("/Mozilla\/\d[\d.]* \([A-Za-z0-9_.\- ;:\/]*\) Gecko\/\d+/i", $user_agent);
    $webkit = preg_match("/Mozilla\/\d[\d.]* \([A-Za-z0-9_.\- ;:\/]*\) AppleWebKit\/\d[\d.]* \(KHTML, like Gecko\)/i", $user_agent);
    if(preg_match_all("/\w+\/\d[\d.]*/", $user_agent, $matches)) {
        $browser = preg_split("/\//",$matches[0][array_key_last($matches[0])]);
        $trident = preg_match("/trident/i", $browser[0]) && !$gecko && !$webkit;
        if($webkit) {
            if(preg_match("/safari/i", $browser[0])) {
                $browser = preg_split("/\//",$matches[0][2]);
                $i = 3;
                while((preg_match("/version/i", $browser[0]) || preg_match("/mobile/i", $browser[0])) && isset($matches[0][$i])) {
                    $browser = preg_split("/\//",$matches[0][$i]);
                    $i++;
                }
            }
        }
    }
    if(preg_match("/\([A-Za-z0-9_.\- ;:\/]*\)/", $user_agent, $match)) {
        $platforms = preg_split("/; /", preg_replace("/\)/", "", preg_replace("/\(/", "", $match[0])));
        if($trident) {
            $browser = preg_split("/ /",$platforms[1]);
            if(preg_match("/msie/i", $browser[0])) {
                $os = preg_split("/ \d/", preg_replace("/ nt/i", "",$platforms[2]));
                $osv = preg_split("/ /",$platforms[2]);
                if(preg_match("/xbox/i", $platforms[array_key_last($platforms)])) {
                    $result["device"]["name"] = $platforms[array_key_last($platforms)];
                }
            } else {
                $browser[0] = "msie";
                $version = preg_split("/:/", $platforms[array_key_last($platforms)]);
                $browser[1] = $version[1];
            }
        }
        if(preg_match("/windows/i", $platforms[0])) {
            $os = preg_split("/ \d/", preg_replace("/ nt/i", "",$platforms[0]));
            $osv = preg_split("/ /",$platforms[0]);
            if(preg_match("/phone/i", $os[0])) {
                $result["device"]["name"] = $platforms[array_key_last($platforms)-1]." ".$platforms[array_key_last($platforms)];
            }
            if(preg_match("/xbox/i", $platforms[array_key_last($platforms)])) {
                $result["device"]["name"] = $platforms[array_key_last($platforms)];
            }
            if(isset($platforms[2]) && preg_match("/x\d[\d]*/", $platforms[2])) {
                $result["device"]["cpu"] = $platforms[2];
            }
        } else if(preg_match("/linux/i", $platforms[0])) {
            $i = preg_match("/u/i", $platforms[1]) ? 2 : 1;
            $os = preg_split("/ \d/",$platforms[$i]);
            if(preg_match("/android/i", $os[0])) {
                $osv = preg_split("/ /",$platforms[$i]);
            } else {
                $os = preg_split("/ /",$platforms[0]);
                if(isset($os[1])) {
                    $result["device"]["cpu"] = $os[1];
                }
            }
            foreach ($platforms as $property) {
                if(preg_match("/build/i", $property)) {
                    $device = preg_split("/ build/i", $property);
                    $result["device"]["name"] = $device[0];
                }
            }
        } else if(preg_match("/linux/i", $platforms[1]) || preg_match("/cros/i", $platforms[1]) || preg_match("/ubuntu/i", $platforms[1])) {
            $os = preg_split("/ /",$platforms[1]);
            if(isset($os[1])) {
                $result["device"]["cpu"] = $os[1];
            }
        } else if(preg_match("/macintosh/i", $platforms[0])) {
            $os = preg_split("/ \d/",preg_replace("/intel /i", "", $platforms[1]));
            $osv = preg_split("/ /",$platforms[1]);
            $result["device"]["name"] = $platforms[0];
        } else if(preg_match("/iphone/i", $platforms[0]) || preg_match("/ipad/i", $platforms[0]) || preg_match("/ipod/i", $platforms[0])) {
            $os = preg_split("/ \d/",preg_replace("/cpu /i", "", $platforms[1]));
            $osv = preg_split("/ /", preg_replace("/ like mac os x/i", "", $platforms[1]));
            $result["device"]["name"] = $platforms[0];
        } else if(preg_match("/android/i", $platforms[0])) {
            $os = preg_split("/ \d/",$platforms[0]);
            $osv = preg_split("/ /",$platforms[0]);
            $result["device"]["name"] = $platforms[1];
        }
        if(isset($os)) {
            $result["os"]["name"] = $os[0];
        }
        if(isset($osv)) {
            $result["os"]["version"] = $osv[array_key_last($osv)];
        }
    }
    if(isset($browser)) {
        $result["browser"]["name"] = $browser[0];
        $result["browser"]["version"] = $browser[1];
    }
    $result["is_mobile"] = preg_match('/mobile/i', $user_agent) ? 1 : 0;
    $result["is_bot"] = (preg_match('/bot/i', $user_agent) || preg_match('/crawler/i', $user_agent)) ? 1 : 0;
    return $result;
}

class user_agent_analyser {
    private $user_agent;
    private $gecko = null;
    private $webkit = null;
    private $trident = null;
    private $browser = null;
    private $platform = null;
    private $mobile = null;
    private $bot = null;

    function get_user_agent() {
        return $this->user_agent;
    }

    function is_mobile() {
        if($this->mobile == null) {
            $this->mobile = preg_match('/mobile/i', $this->user_agent) ? 1 : 0;
        }

        return $this->mobile;
    }

    function is_bot() {
        if($this->bot == null) {
            $this->bot = (preg_match('/bot/i', $this->user_agent) || preg_match('/crawler/i', $this->user_agent)) ? 1 : 0;
        }

        return $this->bot;
    }

    function is_gecko() {
        if($this->gecko == null) {
            $this->gecko = preg_match("/Mozilla\/\d[\d.]* \([A-Za-z0-9_.\- ;:\/]*\) Gecko\/\d+/i", $this->user_agent);
        }

        return $this->gecko;
    }

    function is_webkit() {
        if($this->webkit == null) {
            $this->webkit = preg_match(
                "/Mozilla\/\d[\d.]* \([A-Za-z0-9_.\- ;:\/]*\) AppleWebKit\/\d[\d.]* \(KHTML, like Gecko\)/i",
                $this->user_agent
            );
        }

        return $this->webkit;
    }

    private function get_browser_array() {
        if($this->browser == null && preg_match_all("/\w+\/\d[\d.]*/", $this->user_agent, $matches)) {
            $this->browser = preg_split("/\//", $matches[0][array_key_last($matches[0])]);
            $this->trident = preg_match("/trident/i", $this->browser[0]) && !$this->is_gecko() && !$this->is_webkit();
            if ($this->webkit) {
                if (preg_match("/safari/i", $this->browser[0])) {
                    $this->browser = preg_split("/\//", $matches[0][2]);
                    $i = 3;
                    while ((preg_match("/version/i", $this->browser[0]) || preg_match("/mobile/i", $this->browser[0]))
                        && isset($matches[0][$i])) {
                        $this->browser = preg_split("/\//", $matches[0][$i]);
                        $i++;
                    }
                }
            } else if($this->trident) {
                $this->browser = preg_split("/ /",$this->get_platform_array()[1]);
                if(!preg_match("/msie/i", $this->browser[0])) {
                    $this->browser[0] = "msie";
                    $version = preg_split("/:/", $this->platform[array_key_last($this->platform)]);
                    $this->browser[1] = $version[1];
                }
            }
        }

        return $this->browser;
    }

    private function get_platform_array() {
        if ($this->platform == null
            && preg_match("/\([A-Za-z0-9_.\- ;:\/]*\)/", $this->user_agent, $match)) {
                $this->platform = preg_split("/; /",
                    preg_replace("/\)/", "", preg_replace("/\(/", "", $match[0]))
                );
        }

        return $this->platform;
    }

    function is_trident() {
        if($this->trident == null) {
            $this->get_browser_array();
        }

        return $this->trident;
    }

    function get_browser_name() {
        if($this->browser == null && $this->get_browser_array() == null) {
            return null;
        }

        return $this->browser[0];
    }

    function get_browser_version() {
        if($this->browser == null && $this->get_browser_array() == null) {
            return null;
        }

        return $this->browser[1];
    }

    function get_browser() { return $this->get_browser_name(); }

    function __construct($user_agent) {
        $this->user_agent = $user_agent;
    }
}