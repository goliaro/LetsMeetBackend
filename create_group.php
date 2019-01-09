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
$name = $_POST['name'];
$description = $_POST['description'];


// check that the group name was filled in
if ($name == "")
{
	echo 'group name must be filled in.';
	exit;
}

// check if the image upload was successful
if (isset($_POST["submit"]) && !empty($_FILES["file"]["name"]))
{
  //print_r($_FILES); print("\n");
}
else
{
    echo 'Please select a file to upload.';
    return;
}

// create the group
if (create_group($name, $description)) {
	echo "New group was created with success.";
}


?>