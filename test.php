<?php

include "uaa.php";

print_r($_SERVER["HTTP_USER_AGENT"]);
echo "<br>";
print_r(analyse_user_agent($_SERVER["HTTP_USER_AGENT"]));