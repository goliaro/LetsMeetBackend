<?php

require_once('user_auth_fns.php');
require_once('activity_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

// variables
$activity_id = $_POST['activity_id'];
$new_member_username = $_SESSION['valid_user'];

// check that the information was filled in
if ($activity_id == "" || $new_member_username == "")
{
	echo 'activity id and new_member_username must be filled in.';
	exit;
}

$conn = db_connect();

// check that the new member is a registered user
$result = $conn->query("select * from users where username='".$new_member_username."'");
if (!$result || $result->num_rows == 0)
{
	echo 'you must be a registered user to join an activity';
	exit;
}

// check to make sure that the activity exists
$result = $conn->query("select group_name from activities_info where activity_id = '".$activity_id."'");
if (!$result || $result->num_rows == 0)
{
	print ("the activity does not exist");
	return false;
}

// get activity's group name
$group_name = $result->fetch_object()->group_name;

if ($group_name == "")
{
	print("could not obtain group_name.");
	return false;
}

// check that the new member is part of the group
$result = $conn->query("select * from group_members where member_username = '".$new_member_username."' and group_name = '".$group_name."'");
if (!$result || $result->num_rows == 0)
{
	print("you must be a member of the group '".$group_name."' to join an activity");
	exit;
}

// join the activity
if (join_activity($activity_id, $new_member_username)) {
	echo "You successfully joined the activity.";
}


?>