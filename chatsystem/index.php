<?php
//Connect to db
include('includes/config/connection.php');

#Start session
session_start();

// Redirect to homepage if not logged in
if(isset($_SESSION['user_type'])) {
	if($_SESSION['user_type'] == 0) {header('Location: lecturerhome.php');}
	if($_SESSION['user_type'] == 1) {header('Location: studenthome.php');}
}

function check_user_input($input_data) {
   $output = trim($input_data);
   $output = stripslashes($output);
   $output = htmlspecialchars($output);
   return $output;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['login'])) {
		$user_number = check_user_input($_POST['usernameAJAX']);
		$password = MD5(check_user_input($_POST['passwordAJAX']));
		
		$query = "SELECT * FROM users WHERE user_number = '$user_number'";
		$result = mysqli_query($dbc, $query);
		
		if(mysqli_num_rows($result) == 1){ 	// If user exists in db
			$user = mysqli_fetch_assoc($result);
			
			if($user['user_password'] == $password){
				include ('Chat.php');
				$chat = new Chat();
				$_SESSION = $user;
				$chat->updateUserOnline($user['user_id'], 1);
				$lastInsertId = $chat->insertUserLoginDetails($user['user_id']);
				$_SESSION['login_details_id'] = $lastInsertId;
				
				if($_SESSION['user_type'] == 0) {exit("lecturerhome.php");}
				if($_SESSION['user_type'] == 1) {exit("studenthome.php");}
			} else {
				exit('passworderror');
			}
				
		}else { 							// If user does not exist in db
				exit("nousererror");
		 }			 
	}		
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
                <a class="navbar-brand" href="index.php">Unizulu Chat</a>                
            </div>
        </nav>
		
        <!-- Page content-->
        <div class="container-fluid ps-md-0">
		  <div class="row g-0">
			<div class="d-none d-md-flex col-md-4 col-lg-6 bg-image">
				<img id="glow" src="assets/images/logo.png" height="50%" width="45%" 
				     style="margin:block; margin-bottom:auto; margin-top:auto; margin-left:auto; margin-right:auto; "></img>
			</div>
			<div class="col-md-8 col-lg-6">
			  <div class="login d-flex align-items-center py-5">
				<div class="container">
				  <div class="row">
					<div class="col-md-9 col-lg-8 mx-auto">
					  <h3 class="login-heading mb-4">Welcome back!</h3>

					  <!-- Sign In Form -->
					  
						<form action="index.php" method="post" role="form">
							<p id="error"> </p>
							<div class="form-floating mb-3">
							  <input type="number" class="form-control" id="username" placeholder="Username">
							  <label for="username">Username</label>
							</div>						
							<div class="form-floating mb-3">
							  <input type="password" class="form-control" id="password" placeholder="Password">
							  <label for="password">Password</label>
							</div>

							<div class="d-grid">
							  <button id="login" class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" type="button">Sign in</button>
							  <div class="text-center">
								<a class="small" href="guesthome.php">Continue as Guest</a>
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
		<!-- Page content-->
       
        <!-- Bootstrap core JS-->
        <script src="js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
		<script src="js/jquery-3.6.0.js"></script>
    
		<script>
		$(document).ready(function(){
			// Login request
			$("#login").on('click', function() {
				var username = $("#username").val();
				var password = $("#password").val();
				
				$("#username").on('focus', function(){
					$("#error").html("");
				});
				
				$("#password").on('focus', function(){
					$("#error").html("");
				});
				
				
				if(username == "" || password == ""){
					$("#error").html("<p style='color:red'>Fields are empty!</p>");
				} else if(username.length == 5 || username.length == 9){	// If input validation checks pass			
					$.ajax({
						type:'POST',
						url:'index.php',
						dataType: "text",
						data:{
							login : 1,
							usernameAJAX : username,
							passwordAJAX : password
						},
						success:function(response){
							if(response == "nousererror"){
								$("#error").html("<p style='color:red'>User does not exist!</p>");
							}else if(response == "passworderror"){
								$("#error").html("<p style='color:red'>Incorrect password!</p>");
							}else {
								window.location.assign(response);
							}
						}
					});
				  } else {
						$("#error").html("<p style='color:red'>Check user input!</p>");
					}
			});	
		});
		</script>
		
    </body>
</html>
