<?php

// include function files for this application
require_once('db_connect.php');

session_start();

// check if we are already logged in
if (isset($_SESSION['valid_user']))  {
      //echo "Logged in as ".$_SESSION['valid_user'].".<br>";
      //echo "You forgot to disable cookies again";
  echo "success_cookie.";
}

else {
      //create short variable names
  $username = $_POST['username'];
  $password = $_POST['password'];
  if ($username == "" || $password == "")
  {
    echo "Username and password must be filled in";
    return;
  }

  try  {
    login($username, $password);
    // no error: can set the session username
    $_SESSION['valid_user'] = $username;
  }
  catch(Exception $e)  {
    // unsuccessful login
    echo 'You could not be logged in.';
    exit;
  }
  
}

?>
