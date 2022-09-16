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
        <title>Student central</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)--> 
        <link href="css/styles.css" rel="stylesheet" />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/mycss.css" rel="stylesheet" />
		<!-- Theme style -->
		<link rel="stylesheet" href="css/adminlte.css">
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="studenthome.php">Unizulu Chat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
						<li class="nav-item"><a class="nav-link" href="studenthome.php">Home</a></li>                           
						<li class="nav-item"><a class="nav-link" href="includes/scripts/logout.php">Logout</a></li>                           
                    </ul>
                </div>
            </div>
        </nav>
		
        <!-- Page content-->
		<style>
			#chat_convo{
				max-height: 65vh;
			}
			#chat_convo .direct-chat-messages{
				min-height: 250px;
				height: inherit;
			}
			#chat_convo .card-body {
				overflow: auto;
			}
		</style>
	
		<h3 class="login-heading mb-4">
		   <div class="bd-example">
			<nav1 aria-label="breadcrumb">
			  <ol1 class="breadcrumb">
				<li1 class="breadcrumb-item active" aria-current="page">Student Home | <?php echo $_SESSION['user_number']; ?></li>
			  </ol1>
			</nav1>
			</div>
		</h3>
				
		<div class="container-fluid py-1">
			<div class="row">
				<div class="col-sm-1 col-md-1 col-lg-1"></div>				  
				  <div class="col-sm-12 col-md-10 col-lg-6">
					  <div class="col">
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
					   </div>
				  </div>
				  
				  <div class="col-12 col-sm-12 col-md-12 col-lg-4">
					<div class="card direct-chat direct-chat-primary" id="chat_convo">
					  <div class="card-header ui-sortable-handle" style="cursor: move;">
						<h3 class="card-title">Chatbot</h3>

						<div class="card-tools">
						  <button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						  </button>
						</div>
					  </div>
					  <!-- /.card-header -->
					  <div class="card-body">
						<!-- Conversations are loaded here -->
						<div class="direct-chat-messages">
						  <!-- Message. Default to the left -->
						  <div class="direct-chat-msg mr-4">
							<img class="direct-chat-img border-1 border-primary" src="assets/images/uvatar_1.jpg" alt="message user image">
							<!-- /.direct-chat-img -->
							<div class="direct-chat-text"> Hi there, how can I help you?
							  <!--?php echo $_settings->info('intro') ?-->
							</div>
							<!-- /.direct-chat-text -->
						  </div>
						  <!-- /.direct-chat-msg -->
						  
						  <!-- /.contacts-list -->
						</div>
						<div class="end-convo"></div>
						<!-- /.direct-chat-pane -->
					  </div>
					  <!-- /.card-body -->
					  <div class="card-footer">
						<form id="send_chat" >
						  <div class="input-group">
							<textarea type="text" name="message" placeholder="Type Message ..." class="form-control" required=""></textarea>
							<span class="input-group-append">
							  <button type="submit" class="btn btn-primary">Send</button>
							</span>
						  </div>
						</form>
					  </div>
					  <!-- /.card-footer-->
					</div>
				</div>
			</div>
		</div>
				
		<div class="d-none" id="user_chat">
			<div class="direct-chat-msg right  ml-4">
				<img class="direct-chat-img border-1 border-primary" src="assets/images/uvatar_1.jpg" alt="message user image">
				<!-- /.direct-chat-img -->
				<div class="direct-chat-text"></div>
				<!-- /.direct-chat-text -->
			</div>
		</div>
		<div class="d-none" id="bot_chat">
			<div class="direct-chat-msg mr-4">
				<img class="direct-chat-img border-1 border-primary" src="assets/images/uvatar_1.jpg" alt="message user image">
				<!-- /.direct-chat-img -->
				<div class="direct-chat-text"></div>
				<!-- /.direct-chat-text -->
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
			var user_number = <?php echo $_SESSION['user_number'] ?>;
			var query = "SELECT modules.module_id, modules.module_code, modules.module_description, users.user_number FROM modules INNER JOIN user_modules ON user_modules.module_id = modules.module_id INNER JOIN users ON users.user_id=user_modules.user_id WHERE users.user_type=1 AND users.user_number ="+user_number; 
			
			// Get student modules
			$.ajax({
				type:'GET',
				url:'includes/scripts/getTableData.php',
				dataType: "json",
				data:{query : query},
				success:function(data){
					var tableData = "";
					
					if(data[0] == "empty"){
						tableData += '<tr><td class="text-center" colspan="4">You havent registered any modules.</td></tr>';
					} else {						
						for(var a = 0; a < data.length; a++) {
							var mod_id = data[a].module_id;
							var mod_code = data[a].module_code;
							var mod_desrciption = data[a].module_description;
							
							tableData += '<tr>';
								tableData += '<th scope="row">' + (a+1) + '</th>';
								tableData += '<td><a id="'+mod_id+'" href="aboutmodule.php?id='+mod_id+'">' + mod_code + '</a></td>';
								tableData += '<td>' + mod_desrciption + '</td>';
								tableData += '<td><a href="studentdiscussion.php?id='+mod_id+'" class="btn btn-outline-secondary">Discussions</a></td>';
								tableData += '<td><a href="viewfiles.php?id='+mod_id+'" class="btn btn-outline-secondary">Files</a></td>';
							tableData += '</tr>';
						}
					}
					document.getElementById("tableRowData").innerHTML += tableData;
				}
			});
			

			// Chatbot requests
			$('#send_chat').submit(function(e){
				e.preventDefault();
				var message = $('[name="message"]').val();
				if(message == '' || message == null) return false;
				var uchat = $('#user_chat').clone();
				uchat.find('.direct-chat-text').html(message);
				$('#chat_convo .direct-chat-messages').append(uchat.html());
				$('[name="message"]').val('')
				$("#chat_convo .card-body").animate({ scrollTop: $("#chat_convo .card-body").prop('scrollHeight') }, "fast");
				
				$.ajax({
					type:'GET',
					url:'includes/scripts/chatbot.php',
					dataType: "text",
					data:{message : message},
					success:function(response){
						if(response){
							var bot_chat = $('#bot_chat').clone();
							bot_chat.find('.direct-chat-text').html(response);
							$('#chat_convo .direct-chat-messages').append(bot_chat.html());
							$("#chat_convo .card-body").animate({ scrollTop: $("#chat_convo .card-body").prop('scrollHeight') }, "fast");
						}
					}
				});
			});			
		});
		</script>		
		
    </body>
</html>
