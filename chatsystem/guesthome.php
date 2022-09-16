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
		<!-- Theme style -->
		<link rel="stylesheet" href="css/adminlte.css">
    </head>
    <body  style="background:#F8F8F8">
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			 <a class="navbar-brand" href="index.php">Unizulu Chat</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" href="index.php">Sing in</a></li>            
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
	<div class="container-fluid py-5">
		<div class="row">
			<div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
				<div class="card direct-chat direct-chat-primary" id="chat_convo">
				  <div class="card-header ui-sortable-handle" style="cursor: move;">
					<h3 class="card-title">Guest | Chatbot</h3>

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
	<!-- Page Content -->				  
				  
	<!-- Bootstrap core JS-->
	<script src="js/bootstrap.bundle.min.js"></script>
	<!-- Core theme JS-->
	<script src="js/scripts.js"></script>
	<!-- jQuery CDN -->
	<script src="js/jquery-3.6.0.js"></script>
	
	<script>				
	$(document).ready(function(){		
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
