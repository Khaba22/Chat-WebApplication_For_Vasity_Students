<?php
session_start();
//Connect to db
include('includes/config/connection.php');

function check_message($input_data) {
   $output = trim($input_data);
   $output = stripslashes($output);
   $output = htmlspecialchars($output);
   return $output;
}

// Module id corresponding files
$module_id = $_GET['id'];

// Count total files
$countfiles = count($_FILES['files']['name']);

// Upload directory
$upload_location = "uploads/";

// To store uploaded files path
$files_arr = array();

// Loop all files
for($index = 0;$index < $countfiles;$index++){

	if(isset($_FILES['files']['name'][$index]) && $_FILES['files']['name'][$index] != ''){

    	// File name
    	$filename = $_FILES['files']['name'][$index];    	

		// File path
		$path = $upload_location.$filename;

		// Upload file
		if(move_uploaded_file($_FILES['files']['tmp_name'][$index],$path)){
			$files_arr[] = $filename;
			$query = "INSERT INTO module_files (module_id, filename) values ('$module_id','$files_arr[$index]')";				
			$result = mysqli_query($dbc, $query);
			
			// Insert to chat and broadcast to students and discussion
			$broadcast = $_SESSION['modcode'];
			$file_id = mysqli_insert_id($dbc);
			$message = $filename.' uploaded! <a href="uploads/'.$filename.'" download>Click here to download!</a>';
			$status = 1;			
			
			$bcast_query = "INSERT INTO chat (sender_userid, reciever_userid, broadcast, file_id, message, status) 
			values ('0','0','$broadcast','$file_id', '$message', '1')";			
			$bcast_result = mysqli_query($dbc, $bcast_query);
		}       
    }		   	
}

echo json_encode($files_arr);
die;