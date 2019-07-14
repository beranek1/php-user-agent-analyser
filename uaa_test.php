<?php

include "ua.php";

print_r($_SERVER["HTTP_USER_AGENT"]);
echo "\n";
print_r(analyse_user_agent($_SERVER["HTTP_USER_AGENT"]));