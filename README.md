# UAA: User Agent Analyser
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
array ( "browser" => array ( "name" => "Firefox", "version" => "68.0" ) )
```
