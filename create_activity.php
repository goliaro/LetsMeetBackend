<?php

require_once('user_auth_fns.php');
require_once('activity_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

// save variables from post
$activity_name = $_POST['activity_name'];
$description = $_POST['description'];
$group_name = $_POST['group_name'];
$owner = $_SESSION['valid_user'];
$place = $_POST['place'];
$starting_time = $_POST['starting_time'];
$ending_time = $_POST['ending_time'];

// check that the information was filled in
if ($activity_name == "" || $group_name == "" || $place == "" || $starting_time == "")
{
	echo 'activity name, group name, place, and starting_time must be filled in.';
	exit;
}

$conn = db_connect();

// check that the owner is a registered user
$result = $conn->query("select * from users where username='".$owner."'");
if (!$result || $result->num_rows == 0)
{
	echo 'you must be a registered user to create an activity';
	exit;
}

// check that the owner is part of the group
$result = $conn->query("select * from group_members where group_name = '".$group_name."' and member_username = '".$owner."'");

if (!$result || $result->num_rows == 0)
{
	print("you must be a member of the group '".$group_name."' to create an activity");
	exit;
}

// create the group
if (create_activity($activity_name, $description, $group_name, $owner, $place, $starting_time, $ending_time)) {
	echo "New activity was created with success.";
}


?>