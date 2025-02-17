<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Error Log</h1>";
echo "<pre>";
print_r(error_get_last());
echo "</pre>";
?> 