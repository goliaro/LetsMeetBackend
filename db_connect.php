<?php

function db_connect() {
   $result = new mysqli('host', 'username', 'password', 'database name');
   if (!$result) {
     throw new Exception('Could not connect to database server');
   } else {
     return $result;
   }
}

require_once('data_valid_fns.php'); 
require_once('user_auth_fns.php');
require_once('group_fns.php');

?>
