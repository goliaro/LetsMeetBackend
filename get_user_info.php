<?php

require_once('user_auth_fns.php');
session_start();
// check to make sure we are logged in. otherwise, error message
if (!check_valid_user())
{
  exit;
}

$username = $_SESSION['valid_user'];
$conn = db_connect();
$result = $conn->query("SELECT name, username, email FROM `users` WHERE username = '".$username."'");

if (!$result || $result->num_rows == 0) {
	throw new Exception('could not find user data');
}
  
$final_array = array();
$tempArray = array();

// Loop through each row in the result set
while($row = $result->fetch_object())
{
	// Add each row into our results array
	$tempArray = $row;
	array_push($final_array, $tempArray);
}

echo json_encode($final_array);

?>