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
$member_username = $_SESSION['valid_user'];

// check that the information was filled in
if ($activity_id == "" || $member_username == "")
{
	echo 'activity id and member_username must be filled in.';
	exit;
}

$conn = db_connect();

// check that the member is a registered user
$result = $conn->query("select * from users where username='".$member_username."'");
if (!$result || $result->num_rows == 0)
{
	echo 'you must be a registered user to leave an activity';
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
$result = $conn->query("select * from group_members where member_username = '".$member_username."' and group_name = '".$group_name."'");
if (!$result || $result->num_rows == 0)
{
	print("you must be a member of the group '".$group_name."' to leave an activity");
	exit;
}

// check to make sure that the member has previously joined the activity
$result = $conn->query("select * from activity_members where activity_id = '".$activity_id."' and member_username = '".$member_username."'");
if (!$result || $result->num_rows == 0)
{
	print("the member is not signed up for the activity.");
	exit;
}

// remove the member
$result = $conn->query("delete from activity_members where activity_id = '".$activity_id."' and member_username = '".$member_username."'");
if (!$result)
{
	print("could not remove you from the activity.");
	exit;
}
else {
	print("Successfully removed you from the activity.");
	exit;
}


?>