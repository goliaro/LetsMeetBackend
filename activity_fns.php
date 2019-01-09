<?php

require_once('db_connect.php');

function create_activity($activity_name, $description, $group_name, $owner, $place, $starting_time, $ending_time) {
  
  if (!$owner)
  {
    print("You must be logged in to create a new group");
    return false;
  }

  $conn = db_connect();

  // check not a repeat activity
  $result = $conn->query("select * from activities_info where name = '".$activity_name."' and owner = '".$owner."' and group_name = '".$group_name."'
  							and place = '".$place."' and starting_time = '".$starting_time."'");
  if ($result && ($result->num_rows>0)) {
    print("A duplicate activity to '".$activity_name."'. already exists. It has the same name, owner, group, place and starting_time.");
    return false;
  }
  

  if (! ($conn->query("INSERT INTO activities_info (name, group_name, owner, place, description, starting_time, ending_time) 
  						VALUES ('".$activity_name."', '".$group_name."', '".$owner."', '".$place."', '".$description."', 
  						'".$starting_time."', '".$ending_time."')"))) {
    print("New activity could not be created.");
    return false;
  }

  // get the activity_id
  $result = $conn->query("select activity_id from activities_info where name = '".$activity_name."' and group_name = '".$group_name."'
  						and owner = '".$owner."' and place = '".$place."' and starting_time = '".$starting_time."'");

  if (!$result || $result->num_rows != 1)
  {
  	print("the database did not return a valid activity_id.");
  	$conn->query("delete from activities_info where name = '".$activity_name."' and owner = '".$owner."' and group_name = '".$group_name."' and place = '".$place."' and starting_time = '".$starting_time."'");
  	return false;
  }

  $activity_id = $result->fetch_object()->activity_id;

  // insert the new member
  if (!$conn->query("insert into activity_members values ('".$activity_id."', '".$owner."')"))
  {
    print("Owner could not be added to the activity");
    
    //delete activity from activity
    $result = $conn->query("delete from activities_info where name = '".$activity_name."' and owner = '".$owner."' and group_name = '".$group_name."' and place = '".$place."' and starting_time = '".$starting_time."'");
    
    return false;
  }

  return true;
}

function join_activity($activity_id, $new_member_username) {
  
  // Add new member to a given activity
  if (!$new_member_username)
  {
    print("You must be logged in to join an activity");
    return false;
  }

  $conn = db_connect();

  // check to make sure that the activity exists
  $result = $conn->query("select * from activities_info where activity_id = '".$activity_id."'");
  if (!$result || $result->num_rows == 0)
  {
  	print ("the activity does not exist");
  	return false;
  }

  // check to make sure that the new_member has not already joined the activity
  $result = $conn->query("select * from activity_members where activity_id = '".$activity_id."' and member_username = '".$new_member_username."'");
  if ($result && $result->num_rows > 0)
  {
  	print ("you have already joined the activity");
  	return false;
  }

  // insert the new member
  $result = $conn->query("insert into activity_members values('".$activity_id."', '".$new_member_username."')");
  if (!$result)
  {
  	print ("could not add you to the activity.");
  	return false;
  }

  return true;

}



?>