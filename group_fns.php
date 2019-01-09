<?php
require_once('db_connect.php');

function get_user_groups($username) {
  //extract from the database all the URLs this user has stored

  $conn = db_connect();
  $result = $conn->query("SELECT name, owner, description FROM `groups_info` JOIN `group_members` 
                          WHERE group_members.member_username = '".$username."' AND group_members.group_name = groups_info.name");

  //on group_members.member_username = '".$username."' and group_members.group_name = groups_info.name
  if (!$result) {
    return false;
  }
  
  $groups_array = array();
  $tempArray = array();

  // Loop through each row in the result set
  while($row = $result->fetch_object())
  {
    // Add each row into our results array
    $tempArray = $row;
    array_push($groups_array, $tempArray);
  }

  return $groups_array;
}

function add_member_to_group($group_name, $new_member_username) {
  
  // Add new member to a given group

  $conn = db_connect();


  // check that you are a member of that group
  $current_user = $_SESSION['valid_user'];
  $result = $conn->query("select * from group_members where group_name = '".$group_name."' and member_username = '".$current_user."'");

  if (!$result || !$result->num_rows)
  {
    print("Error: either you are not a member of the group '".$group_name."', or the group does not exist.");
    return false;
  }

  // check that the new member is a registered user
  $result = $conn->query("select * from users where username = '".$new_member_username."'");
  if (!$result || $result->num_rows == 0)
  {
    print("Error: '".$new_member_username."' is not a registered user.");
    return false;
  }

  // check not a repeat member
  $result = $conn->query("select * from group_members where group_name = '".$group_name."' and member_username = '".$new_member_username."'");
  if ($result && ($result->num_rows>0)) {
    print ("User '".$new_member_username."' is already in the group '".$group_name."'.");
    return false;
  }

  // insert the new member
  if (!$conn->query("insert into group_members values ('".$group_name."', '".$new_member_username."')")) {
    print("New member could not be added.");
    return false;
  }

  return true;
}

function delete_group($group_name) {

  $conn = db_connect();

  $current_user = $_SESSION['valid_user'];

  // check that you are the owner of the group
  $result = $conn->query("select * from groups_info where name = '".$group_name."' and owner = '".$current_user."'");
  if (!$result || $result->num_rows == 0)
  {
    print("You must be the owner of the group '".$group_name."' in order to delete the group");
    return false;
  }

  // remove all the members
  if (!$conn->query("delete from group_members where group_name = '".$group_name."'")) {
    print("Removal of all the members could not be completed");
    return false;
  }

  // remove the group from the group_info table
  $result = $conn->query("delete from groups_info where name = '".$group_name."'");
  if (!$result) {
    print("Removal of the group '".$group_name."' from the groups_info table could not be completed");
    return false;
  }

  return true;
}

function remove_member_from_group($group_name, $member_username) {
  
  // Add new member to a given group

  $conn = db_connect();

  $current_user = $_SESSION['valid_user'];
  // check that you are the owner of the group
  $result = $conn->query("select * from groups_info where name = '".$group_name."' and owner = '".$current_user."'");
  if ((!$result || $result->num_rows == 0) && $member_username != $current_user)
  {
    print("You must be the owner of the group '".$group_name."' in order to remove a member");
    return false;
  }

  // if the member we want to remove is the owner himself, we need to delete the entire group
  // we already checked that the current user is the owner of the group
  else if (($result && $result->num_rows > 0) && $member_username == $current_user) 
  {
    return delete_group($group_name);
  }
  else 
  {
    // check that member is in the group
    $result = $conn->query("select * from group_members where group_name = '".$group_name."' and member_username = '".$member_username."'");
    if (!$result || ($result->num_rows == 0)) {
      print("User '".$member_username."' is not in the group '".$group_name."'.");
      return false;
    }

    // delete the member
    if (!$conn->query("delete from group_members where group_name = '".$group_name."' and member_username = '".$member_username."'")) {
      print("Member '".$member_username."' could not be removed");
      return false;
    }
  }

  

  return true;
}

function create_group($name, $description) {
  
  // Add new member to a given group
  $owner = $_SESSION['valid_user'];
  if (!$owner)
  {
    print("You must be logged in to create a new group");
    return false;
  }

  $conn = db_connect();

  // check not a repeat member
  $result = $conn->query("select * from groups_info where name = '".$name."'");
  if ($result && ($result->num_rows>0)) {
    print("A group with name '".$group_name."'. already exists");
    return false;
  }
  

  if (! ($conn->query("INSERT INTO groups_info (owner, name, description) VALUES ('".$owner."', '".$name."', '".$description."')"))) {
    print("New group could not be created.");
    return false;
  }

  // add the owner to the group

  // insert the new member
  if (!$conn->query("insert into group_members values ('".$name."', '".$owner."')"))
  {
    print("Owner could not be added to the group");
    //delete group from groupinfo
    $result = $conn->query("delete from groups_info where name = '".$name."'");
    
    return false;
  }

  // time to upload the image
  if (!save_group_image()) {
    if (!$conn->query("delete from groups_info where name = '".$name."'")) {
      print("removal of group, whose photo could not be uploaded, from groups_info table failed.");
      return false;
    } 
    else if (!$conn->query("delete from group_members where group_name = '".$name."'")) {
      print("removal of group, whose photo could not be uploaded, from group_members table failed.");
      return false;
    }
    
  }

  return true;
}

function save_group_image() {

  // File upload path
  $targetDir = "./uploads/groups_pictures/";
  $fileName = basename($_FILES["file"]["name"]);    //print($fileName); print("\n");
  $targetFilePath = $targetDir . $fileName;         //print($targetFilePath); print("\n");
  //print($_FILES["file"]["name"]); print("\n");
  $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

  // Allow certain file formats
  $allowTypes = array('jpg','png','jpeg','gif');
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
