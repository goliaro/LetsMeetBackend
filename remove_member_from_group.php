<?php

require_once('user_auth_fns.php');
require_once('group_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

// save variables from post
$group_name = $_POST['group_name'];
$member_username = $_POST['member_username'];
// check that the group name was filled in

if ($group_name == "" || $member_username == "")
{
	echo "group name and member username must be filled in";
	exit;
}

// create the group
if (remove_member_from_group($group_name, $member_username)) {
	echo "Successfully removed the user.";
}
else {
	exit;
}


?>