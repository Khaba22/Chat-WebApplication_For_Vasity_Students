<?php
session_start();

// Redirect to index if not logged in
if(!isset($_SESSION['user_type'])){
	if($_SESSION['user_type'] <> 1) {header('Location: index.php');}
}
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
							<li class="breadcrumb-item active" aria-current="page">Lecturer Home | <?php echo $_SESSION['user_number'] ?></li>
						  </ol>
						</nav>
						</div>
						</h3>					  					  

						<form>					  
							<div class="bd-example">
							<table class="table table-striped table-borderless">
							  <thead>
							  <tr>
								<th scope="col">#</th>
								<th scope="col">Module</th>
								<th scope="col">Description</th>
								<th scope="col"></th>
								<th scope="col"></th>
							  </tr>
							  </thead>
							  <tbody id = "tableRowData">
								<!-- Table data added asynchronously by ajax -->
							  </tbody>
							</table>
						   </div>
						</form>
					
					</div>
				  </div>
				</div>
			  </div>
			</div>
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
			// Lecturer modules request
			var user_number = <?php echo $_SESSION['user_number'] ?>;
			var query = "SELECT modules.module_id, modules.module_code, modules.module_description, users.user_number FROM modules INNER JOIN user_modules ON user_modules.module_id = modules.module_id INNER JOIN users ON users.user_id=user_modules.user_id WHERE users.user_type=0 AND users.user_number ="+user_number; 
			
			$.ajax({
				type:'GET',
				url:'includes/scripts/getTableData.php',
				dataType: "json",
				data:{query : query},
				success:function(data){
					var tableData = "";
					
					if(data[0] == "empty"){
						tableData += '<tr><td class="text-center">No modules currently assigned to you.</td></tr>';
					} else {
						
						for(var a = 0; a < data.length; a++) {
							var mod_id = data[a].module_id;
							var mod_code = data[a].module_code;
							var mod_desrciption = data[a].module_description;
							
							tableData += '<tr>';
								tableData += '<th scope="row">' + (a+1) + '</th>';
								tableData += '<td>' + mod_code + '</td>';
								tableData += '<td>' + mod_desrciption + '</td>';
								tableData += '<td><a href="lecturerdiscussion.php?id='+mod_id+'" class="btn btn-outline-secondary">Discussions</a></td>';
								tableData += '<td><a href="managefiles.php?id='+mod_id+'" class="btn btn-outline-secondary">Manage</a></td>';
							tableData += '</tr>';
						}
						document.getElementById("tableRowData").innerHTML += tableData;
					}						
				}
			});			
		});
		</script>
		
    </body>
</html>
