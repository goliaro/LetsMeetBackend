<?php

require_once('user_auth_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

require_once('group_fns.php');
$groups_array = get_user_groups($_SESSION['valid_user']);
echo json_encode($groups_array);

?>