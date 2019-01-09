<?php

require_once('user_auth_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

$current_user = $_SESSION['valid_user'];
$group_name = $_POST['group_name'];


if ($group_name == "")
{
	print("Please provide a valid group name.");
	return;
}

$conn = db_connect();

// first of all, check that you are a member of the group, before you are allowed to see the other members
$result = $conn->query("select * from group_members where group_name = '".$group_name."' and member_username = '".$current_user."'");
if (!$result || !$result->num_rows)
{
	print("You must be a member of a given group in order to see the other members");
	return false;
}

$result = $conn->query("SELECT username, name, email FROM `users` JOIN `group_members` WHERE group_members.group_name = '".$group_name."'
						AND users.username = group_members.member_username");
if (!$result) {
	print("could not get members info from groups database");
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