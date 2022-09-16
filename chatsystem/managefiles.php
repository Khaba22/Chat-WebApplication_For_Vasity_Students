<?php
session_start();

// Redirect to index if not logged in
if(!isset($_SESSION['user_type'])){
	if($_SESSION['user_type'] <> 1) {header('Location: index.php');}
}

// Get the current module code for broadcast and deletion
include("includes/config/connection.php");
$result = mysqli_query($dbc, "SELECT module_code FROM modules WHERE module_id=".$_GET['id']);
$data = mysqli_fetch_assoc($result);
$_SESSION['modcode'] = $data['module_code'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Lecturer dashboard</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)--> 
        <link href="css/styles.css" rel="stylesheet" />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/mycss.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                 <a class="navbar-brand" href="lecturerhome.php">Unizulu Chat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="lecturerhome.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
						<li class="nav-item"><a class="nav-link" href="includes/scripts/logout.php">Logout</a></li>                
                    </ul>
                </div>
            </div>
        </nav>
		
        <!-- Page content-->
        <div class="container-fluid ps-md-0">
		  <div class="row g-0">
			<div class="d-none d-md-flex col-md-4 col-lg-6 bg-image">
				<img src="assets/images/logo.png" height="50%" width="45%" 
				     style="margin:block; margin-bottom:auto; margin-top:auto; margin-left:auto; margin-right:auto; "></img>
			</div>
			<div class="col-md-8 col-lg-6">
			  <div class="login d-flex py-5">
				<div class="container">
				  <div class="row">
					<div class="col-md-10 col-lg-11 mx-auto">
					   
					   <h3 class="login-heading mb-4">
					   <div class="bd-example">
						<nav aria-label="breadcrumb">
						  <ol class="breadcrumb">							
							<li id="pageHeading" class="breadcrumb-item active" aria-current="page">Manage |</li>
						  </ol>
						</nav>
						</div>
						</h3>
						
						<form method="post" enctype="multipart/form-data">					  
						   <div class="mb-3">
							<label id="uploadmessage" class="form-label" for="customFile"></label>
							<div class=" input-group mb-3">
							<input type="file" id='files' name="files[]" multiple class="form-control">
							<span class="btn btn-primary" id="upload" type="submit">upload</span>
							</div>
						  </div>					  
						</form>			  
					  
						<div class="bd-example">
							<table id="filesTable" class="table table-dark table-borderless">
							  <thead>
							  <tr>
								<th scope="col">#</th>
								<th scope="col">Filename</th>
								<th scope="col"></th>
								<th scope="col"></th>
							  </tr>
							  </thead>
							  <tbody id = "tableRowData">
								<!-- Table data added asynchronously by ajax -->
							  </tbody>
							</table>
						</div>					   
					   
						<form method="post" role="form">
							<div class="modal fade" id="staticBackdropLive" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLiveLabel" aria-hidden="true">
							  <div id="myModal" class="modal-dialog modal-dialog-centered">
								<div class="modal-content">
								  <div class="modal-header">
									<h5 class="modal-title" id="staticBackdropLiveLabel">Confirm delete</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								  </div>
								  <div class="modal-body">
									<p>Delete this file?</p>
								  </div>
								  <div class="modal-footer">
									<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
									<button id="confirm_delete" type="button" class="btn btn-danger btnModal" data-bs-dismiss="modal">Delete</button>
								  </div>
								</div>
							  </div>
							</div>
						</form>					   
					  
					</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>		
		
		<!-- Notification: Upload successful.	-->			
		<div class="toast bg-primary text-white fade" id="upload_success" style="position: absolute; top:10px; right: 10px;">
			<div class="toast-header bg-primary text-white">
				<strong class="me-auto"><i class="bi-gift-fill"></i> Notification</strong>
				<small><?php echo date("H:i:sa"); ?></small>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
			</div>
			<div class="toast-body">
				File(s) uploaded successfully.
			</div>
		</div>		
		
		<!-- Notification: Delete successful.	-->			
		<div class="toast bg-danger text-white fade" id="delete_success" style="position: absolute; top:10px; right: 10px;">
			<div class="toast-header bg-danger text-white">
				<strong class="me-auto"><i class="bi-gift-fill"></i> Notification</strong>
				<small><?php echo date("H:i:sa"); ?></small>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
			</div>
			<div class="toast-body">
				File deleted successfully.
			</div>
		</div>		
		<!-- Page content-->		
       
        <!-- Bootstrap core JS-->
        <script src="js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
		<!-- jQuery CDN -->
		<script src="js/jquery-3.6.0.js"></script>  
				
		<script>
			$(document).ready(function(){				
				
				//On page load, load all table data_______________________________________
				var module_id = <?php echo $_GET['id']?>;
				var query = "SELECT module_files.file_id, module_files.module_id, module_files.filename, module_files.file_location, modules.module_code FROM `module_files` JOIN modules ON module_files.module_id = modules.module_id WHERE modules.module_id ="+module_id+" ORDER BY module_files.file_id DESC"; 
				getTableData();
				
				function getTableData(){
					$.ajax({
						type:'GET',
						url:'includes/scripts/getTableData.php',
						dataType: "json",
						data:{
							query : query,
							module_id: module_id
							},
						success:function(data){
							var tableData = "";						
							
							if(data[0] == "empty"){
								document.getElementById("pageHeading").innerHTML = 'Manage | '+data[1].module_code;	
								tableData += '<tr>';
									tableData += '<td class="text-center" colspan="4">There is currently no files for this module</td>';
								tableData += '</tr>';
							} else {	
							
								for(var a = 0; a < data.length; a++) {
									var file_id = data[a].file_id;
									var filename = data[a].filename;
									var filepath = data[a].file_location+filename;									
									
									document.getElementById("pageHeading").innerHTML = 'Manage | '+data[0].module_code;	
									tableData += '<tr>';
										tableData += '<th scope="row">' + (a+1) + '</th>';
										tableData += '<td>' + filename + '</td>';
										tableData += '<td><button type="button" class="btn btn-link"><a href="'+filepath+'" download>Download</a></button></td>';
										tableData += '<td><button id="'+file_id+'" type="submit" class="btn btn-outline-danger btnSelected" data-bs-toggle="modal" data-bs-target="#staticBackdropLive">Delete</button></td>';
									tableData += '</tr>';
								}
								
							}
							document.getElementById("tableRowData").innerHTML = tableData;						
						}
					}); 
				}
				
							
				
				// When the uploading__________________________________________________________________________
				$("#files").on('click', function(){
					$("#uploadmessage").html("");
				});
				
				$("#upload").on('click', function(){
					var selected_file = $("#files").val();
					
					if(selected_file == ""){
						$("#uploadmessage").html("<p style='color : red'>Please select files first! </p>");
					} else {						
						var form_data = new FormData();

						// Read selected files
						var totalfiles = document.getElementById('files').files.length;
						for (var index = 0; index < totalfiles; index++) {
							form_data.append("files[]", document.getElementById('files').files[index]);
						}						
						
						// AJAX request
						$.ajax({
							url: 'ajaxfile.php?id='+module_id,
							type: 'POST',
							data: form_data,
							dataType: 'json',
							contentType: false,
							processData: false,
							success: function (response) {
								getTableData();
								$("#upload_success").toast({delay : 3000});
								$("#upload_success").toast("show");
								$("#files").val("");
								
							}
						});	
					}					
				});				
				
				
				// Deleting a file__________________________________________________________
				var file_id ="";
				
				// Getting the id of the file being deleted
				$("#filesTable").on('click', '.btnSelected', function(){
					file_id	 = $(this).attr("id");
				});
				
				// Confirm delete
				$("#myModal").on('click', '.btnModal', function(){
					$.ajax({
						type: 'POST',
						url:'includes/scripts/deleteFile.php',											
						dataType: 'json',
						data: {myData: file_id},
						success: function (response) {
							getTableData();
							$("#delete_success").toast({delay : 3000});
							$("#delete_success").toast("show");
						}
					});
				});		
								
			});
		</script>			
		
    </body>
</html>
