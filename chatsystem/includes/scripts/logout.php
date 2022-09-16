<?php
#Start session
session_start();

include ('../../Chat.php');
$chat = new Chat();
$chat->updateUserOnline($_SESSION['user_id'], 0);

session_destroy();    //This deletes all the session keys 
header('Location: ../../index.php'); //redirect to home page
?>