<?php
require_once('user_auth_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

$current_user = $_SESSION['valid_user'];
$activity_id = $_POST['activity_id'];

// check that the group_name was provided
if ($activity_id == "")
{
	print("Please provide a valid activity_id.");
	return;
}

$conn = db_connect();

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

// first of all, check that you are a member of the group, before you are allowed to see the group's activities
$result = $conn->query("select * from group_members where group_name = '".$group_name."' and member_username = '".$current_user."'");
if (!$result || !$result->num_rows)
{
	print("You must be a member of a given group in order to see the activities");
	return false;
}

$result = $conn->query("SELECT username, name, email FROM `users` JOIN `activity_members` WHERE activity_members.activity_id = '".$activity_id."'
						AND users.username = activity_members.member_username");
if (!$result) {
	print("could not get members info from activities database");
	return;
}

$members_array = array();
$tempArray = array();

// Loop through each row in the result set
while($row = $result->fetch_object())
{
	// Add each row into our results array
	$tempArray = $row;
	array_push($members_array, $tempArray);
}

echo json_encode($members_array);

?>