# UAA: User Agent Analyser
A php function/library for analysing user agents.

Optimised for Gecko (Firefox), AppleWebKit (Chrome, Safari, Edge) and Trident (Internet Explorer) user agents, but also supports most of the others.

Developed for https://webanalytics.one

## Usage
```php
include "uaa.php"
$uaa = analyse_user_agent($_SERVER["HTTP_USER_AGENT"]);
$browser_name = $uaa["browser"]["name"];
$browser_version = $uaa["browser"]["version"];
```

## Example return value
```php
array ( "browser" => array ( "name" => "Firefox", "version" => "68.0" ), "os" => Array ( "name" => "Windows", "version" => "10.0" ), "device" => array ( "name" => "pc" ) ) 
```
