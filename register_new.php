<?php
  // include function files for this application
  require_once('db_connect.php');

  //create short variable names
  $email=$_POST['email'];
  $username=$_POST['username'];
  $name=$_POST['name'];
  $password=$_POST['password'];
  $password2=$_POST['password2'];

  // start session which may be needed later
  // start it now because it must go before headers
  session_start();
  
  try   {
    // check forms filled in
    
    if ($email == "" || $username == "" || $name == "" || $password == "" || $password2 == "")
    {
      throw new Exception('You must provide data for all fields to register.');
    }

    // email address not valid
    if (!valid_email($email)) {
      throw new Exception('That is not a valid email address.  Please go back and try again.');
    }

    // passwords not the same
    if ($password != $password2) {
      throw new Exception('The passwords you entered do not match - please go back and try again.');
    }

    // check password length is ok
    // ok if username truncates, but passwords will get
    // munged if they are too long.
    if ((strlen($password) < 6) || (strlen($password) > 20)) {
      throw new Exception('Your password must be between 6 and 20 characters. Please go back and try again.');
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

    // attempt to register
    // this function can also throw an exception
    register($username, $name, $email, $password);
    
    // register session variable
    $_SESSION['valid_user'] = $username;

    // provide link to members page
    echo 'Your registration was successful';
   
  }
  catch (Exception $e) {
     echo $e->getMessage();
     exit;
  }
?>
