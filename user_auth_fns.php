<?php

require_once('db_connect.php');

function register($username, $name, $email, $password) {
// register new person with db
// return true or error message

  // connect to db
  $conn = db_connect();

  // check if username is unique
  $result = $conn->query("select * from users where username='".$username."'");
  if (!$result) {
    throw new Exception('Could not execute query');
  }

  if ($result->num_rows>0) {
    throw new Exception('That username is taken - go back and choose another one.');
  }
  
  // if ok, put in db
  $result = $conn->query("insert into users values
                         ('".$username."', '".$name."', '".$email."', '".hash('sha512', $password)."')");

  if (!$result) {
    throw new Exception('Could not register you in database - please try again later.');
  }
  else if (!save_image()) {
    if (!$conn->query("delete from users where username = '".$username."'"))
    {
      print("removal of member, whose photo could not be uploaded, from users table failed.");
    }
    return false;
  }

  return true;
}

function login($username, $password) {
// check username and password with db
// if yes, return true
// else throw exception

  // connect to db
  $conn = db_connect();
  // check if username is unique
  $result = $conn->query("select username, name, email from users
                         where username='".$username."'
                         and password = '".hash('sha512', $password)."'  ");
  if (!$result) {
     throw new Exception('Could not log you in.');
  }

  if ($result->num_rows>0) {
      /*$resultArray = array();
      $tempArray = array();
   
      // Loop through each row in the result set
      while($row = $result->fetch_object())
      {
        // Add each row into our results array
        $tempArray = $row;
        array_push($resultArray, $tempArray);
      }
   
      // Finally, encode the array to JSON and output the results
      echo json_encode($resultArray);*/
      echo "success_login.";
     return true;
  } else {
     throw new Exception('Could not log you in.');
  }
}

function check_valid_user() {
// see if somebody is logged in and notify them if not
  if (isset($_SESSION['valid_user']))  {
      //echo "Logged in as ".$_SESSION['valid_user'].".<br>";
    // do nothing
    return true;
  } else {
     // they are not logged in
     echo 'You are not logged in.';
     return false;
     
  }
}

function save_image() {

  // File upload path
  $targetDir = "./uploads/profile_pictures/";
  $fileName = basename($_FILES["file"]["name"]);    //print($fileName); print("\n");
  $targetFilePath = $targetDir . $fileName;         //print($targetFilePath); print("\n");
  //print($_FILES["file"]["name"]); print("\n");
  $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

  // Allow certain file formats
  $allowTypes = array('jpg','png','jpeg','gif', 'jpg');
  if (in_array($fileType, $allowTypes)) 
  {
      // Upload file to server
      if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath))
      {
        //echo "The file ".$fileName. " has been uploaded successfully.";
        return true;
      } 
      else
      {
        echo "Sorry, there was an error uploading your file.";
        return false;
      }
  }
  else
  {
      echo 'Sorry, only JPG, JPEG, PNG, GIF files are allowed to upload.';
      return false;
  }
  
}

?>
