<?php
//Connect to db
include('../config/connection.php');

$data = array();

if($result = mysqli_query($dbc, $_GET['query'])){
	
	// Load data matched into the array
	while ($row = mysqli_fetch_object($result)){
		array_push($data, $row);
	}
	
	// If no data matched the query
	if(sizeof($data) == 0) {
		array_push($data, "empty");
		
		if(isset($_GET['module_id'])) {
			$result = mysqli_query($dbc, "SELECT module_code FROM modules WHERE module_id =".$_GET['module_id']); 
			$row = mysqli_fetch_object($result);
			array_push($data, $row);
		}
		
	}	
}

echo json_encode($data);
exit();
?>