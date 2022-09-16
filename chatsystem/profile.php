<?php
session_start();

// Redirect to index if not logged in
if(!isset($_SESSION['user_type'])){
	if($_SESSION['user_type'] <> 1) {header('Location: index.php');}
}

// Update profile 
if(isset($_POST['update_query'])) {
	include("includes/config/connection.php");
	$update_query = $_POST['update_query'];
	
	echo mysqli_query($dbc, $update_query);	
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Unizulu - Chat System</title>
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
			  <div class="login d-flex align-items-center py-5">
				<div class="container">
				  <div class="row">
					<div class="col-md-9 col-lg-8 mx-auto">
					   
					   <h3 class="login-heading mb-4">
					   <div class="bd-example">
						<nav aria-label="breadcrumb">
						  <ol class="breadcrumb">
							<li class="breadcrumb-item active" aria-current="page">Profile | <?php echo $_SESSION['user_number'];?></li>
						  </ol>
						</nav>
						</div>
						</h3>
				
						<form method="post">					  
							<div class="form-floating mb-3">
							  <input type="text" class="form-control" id="name" placeholder="Name">
							  <label for="name">Name</label>
							</div>
							<div class="form-floating mb-3">
							  <input type="text" class="form-control" id="surname" placeholder="Surname">
							  <label for="surname">Surname</label>
							</div>
							<div class="form-floating mb-3">
							  <input type="text" class="form-control" id="office" placeholder="Office" disabled>
							  <label for="office">Office</label>
							</div>						

							<div class="d-grid">
							  <button id="update" class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" type="button">update</button>
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
		
		<!-- Notification: Update successful.	-->			
		<div class="toast bg-primary text-white fade" id="update_success" style="position: absolute; top:10px; right: 10px;">
			<div class="toast-header bg-primary text-white">
				<strong class="me-auto"><i class="bi-gift-fill"></i> Notification</strong>
				<small><?php echo date("d/m/Y"); ?></small>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
			</div>
			<div class="toast-body">
				Your profile was updated successfully.
			</div>
		</div>			
		
        <!-- Bootstrap core JS-->
        <script src="js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
		<!-- jQuery CDN -->
		<script src="js/jquery-3.6.0.js"></script>  
				
		<script>		
		// Load page data_______________________________________________________________________
		$(document).ready(function(){
			
			var user_id = <?php echo $_SESSION['user_id']?>;
			var query = "SELECT users.user_name, users.user_surname, lecturers_details.office FROM users JOIN lecturers_details ON lecturers_details.user_id=users.user_id WHERE users.user_type=0 AND users.user_id="+user_id; 
					
			$.ajax({
				type:'GET',
				url:'includes/scripts/getTableData.php',
				dataType: "json",
				data:{query : query},
				success:function(data){
					$("#name").val(data[0].user_name);
					$("#surname").val(data[0].user_surname);
					$("#office").val(data[0].office);
				}
			});			
		
		
			// Update profile_____________________________________________________________________
			$("#update").on('click', function() {
				var name = $("#name").val();
				var surname = $("#surname").val();
				var update_query ="UPDATE users SET user_name='"+name+"', user_surname='"+surname+"' WHERE user_id="+user_id; 
				
				$.ajax({
					type: 'POST',
					url: 'profile.php',
					dataType: "json",
					data:{update_query : update_query},
					success:function(data){						
						$("#update_success").toast({delay : 3000});
						$("#update_success").toast("show");
					}
				});				
			});			
		});
		</script>				
		
    </body>
</html>
