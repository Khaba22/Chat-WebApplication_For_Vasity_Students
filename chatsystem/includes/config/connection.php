<?php
	//Setup File
	$mysql_host = 'localhost';
	$mysql_user = 'root';
	$mysql_password = '';
	$dbname = 'unizulu_chats';
	$dbc = @mysqli_connect($mysql_host,$mysql_user,$mysql_password, $dbname);

	if(mysqli_connect_errno()){
		echo '
		<body style="background:black;">
			<div align="center" style="color:white; margin-top:15%;">
			  <h1 class="error-number">404</h1>
			  <h2>Sorry but we couldn\'t find this page</h2>
			  <p>This page you are looking for does not exist</p>
			  <p>OR</p>
			  <p>There might be a connection problem!</p>
			</div>
		</body>';			
		//mysqli_connect_errno(); error number
		exit();
}

?>