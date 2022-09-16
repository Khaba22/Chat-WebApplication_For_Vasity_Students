<?php
if(isset($_POST["myData"])){
	include("../config/connection.php");
	$file_id = $_POST["myData"];
	
	$query = "SELECT * FROM module_files WHERE file_id = ".$file_id;;
	$result= mysqli_query($dbc, $query);
	$data = mysqli_fetch_assoc($result);
	$filename = $data['filename'];
	$file_location = $data['file_location'];
	
 	$delete_query = "DELETE FROM module_files WHERE file_id = ".$file_id;
	if(mysqli_query($dbc, $delete_query)) {
		echo unlink('../../'.$file_location.$filename);
		
		// Aslo delete the broadcast associated with this file
		$delete_broadcast = "DELETE FROM chat WHERE file_id = ".$file_id;
		mysqli_query($dbc, $delete_broadcast);
		
	} else {
		echo json_encode("failed");
	}
	
exit;
}
?>