# UAA: User Agent Analyser
Open-source PHP library for analysing user agents.

Optimised for Gecko (Firefox), AppleWebKit (Chrome, Safari, Edge) and Trident (Internet Explorer) user agents, but also supports most of the others.

## Usage
```php
include "uaa.php"
$uaa = analyse_user_agent($_SERVER["HTTP_USER_AGENT"]);
$browser_name = $uaa["browser"]["name"];
$browser_version = $uaa["browser"]["version"];
$os_name = $uaa["os"]["name"];
$os_version = $uaa["os"]["version"];
$device = $uaa["device"]["name"];
$cpu = $uaa["device"]["cpu"];
$is_mobile = $uaa["is_mobile"];
$is_bot = $uaa["is_bot"];
```

Try it out with test.php.

## Features
* Detects browser name and version
* Detects operation system (OS) name and version
* Detects device name/identifier and cpu type if given

## Example return value
```php
//Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0
array ( "device" => array ( "cpu" => "x64" ), "os" => array ( "name" => "Windows", "version" => "10.0" ), "browser" => array ( "name" => "Firefox", "version" => "68.0" ), "is_mobile" => 0, "is_bot" => 0)
```