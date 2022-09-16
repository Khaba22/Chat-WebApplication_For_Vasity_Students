<?php
class Chat{
    private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "";
    private $database  = "unizulu_chats";      
    private $chatTable = 'chat';
	private $chatUsersTable = 'users';
	private $chatLoginDetailsTable = 'chat_login_details';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function loginUsers($username, $password){
		$sqlQuery = "
			SELECT user_id, user_name 
			FROM ".$this->chatUsersTable." 
			WHERE user_name='".$username."' AND user_password='".$password."'";		
        return  $this->getData($sqlQuery);
	}		
	public function chatUsers($userid){
		$sqlQuery = "
			SELECT module_code as user_id, module_code as user_name, '' as user_surname,
			avatar, current_session, online FROM `modules` WHERE module_id LIKE ".$_GET['id']."
			UNION
			SELECT users.user_id, users.user_name, users.user_surname, users.avatar, users.current_session, users.online 
			FROM modules INNER JOIN user_modules ON user_modules.module_id = modules.module_id INNER JOIN users ON users.user_id=user_modules.user_id 
			WHERE users.user_id != '$userid' AND modules.module_id LIKE ".$_GET['id'];
		return  $this->getData($sqlQuery);
	}
	public function getUserDetails($userid){							
		$sqlQuery = "
			SELECT module_code as user_id, module_code as user_name, '' as user_surname,
			avatar, current_session, online FROM `modules` WHERE module_code LIKE '$userid'
			UNION
			SELECT user_id, user_name, user_surname, avatar, current_session, online 
			FROM ".$this->chatUsersTable." WHERE user_id LIKE '$userid'";
		return  $this->getData($sqlQuery);
	}
	public function getUserAvatar($userid){
		$sqlQuery = "
			SELECT avatar FROM users 
			WHERE user_id LIKE '$userid' 
			UNION 
			SELECT avatar FROM modules 
			WHERE module_code LIKE '$userid'";
		$userResult = $this->getData($sqlQuery);
		$userAvatar = '';
		foreach ($userResult as $user) {
			$userAvatar = $user['avatar'];
		}	
		return $userAvatar;
	}	
	public function updateUserOnline($userId, $online) {		
		$sqlUserUpdate = "
			UPDATE ".$this->chatUsersTable."
			SET online = '".$online."' 
			WHERE user_id = '".$userId."'";			
		mysqli_query($this->dbConnect, $sqlUserUpdate);		
	}
	public function insertChat($reciever_userid, $user_id, $chat_message) {
		$module_id ='0';
		$chat_message = str_replace("'", "''", $chat_message);
		if(!is_numeric($reciever_userid)){
			$module = $reciever_userid;
		}
		
		$sqlInsert = "
			INSERT INTO ".$this->chatTable." 
			(reciever_userid, sender_userid, message, module, status) 
			VALUES ('".$reciever_userid."', '".$user_id."', '".$chat_message."', '".$module."', '1')";
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(!$result){
			return ('Error in query: '. mysqli_error());
		} else {
			$conversation = $this->getUserChat($user_id, $reciever_userid);
			$data = array(
				"conversation" => $conversation			
			);
			echo json_encode($data);	
		}
	}
	public function getUserChat($from_user_id, $to_user_id) {
		$fromUserAvatar = $this->getUserAvatar($from_user_id);	
		$toUserAvatar = $this->getUserAvatar($to_user_id);
				
		$modcode = $_SESSION['modcode'];
				
		if(!is_numeric($to_user_id)){
			// If chstting on the module
			$module = $to_user_id;
			
			$sqlQuery = "
				SELECT chat.*, users.user_name as username, users.user_surname as surname 
				FROM ".$this->chatTable." JOIN users ON users.user_id=chat.sender_userid 
				WHERE (sender_userid = '".$module."' 
				OR reciever_userid = '".$module."')
				OR module = '".$module."'
				UNION
				SELECT chat.*, '' as username, '' as surname 
				FROM ".$this->chatTable."
				WHERE broadcast LIKE '".$modcode."' ORDER BY timestamp ASC";
		} else {
			// If users are chatting with each other
			
			$sqlQuery = "
				SELECT chat.*, users.user_name as username, users.user_surname as surname 
					FROM ".$this->chatTable." JOIN users ON users.user_id=chat.sender_userid 
					WHERE (sender_userid = '".$from_user_id."' 
					AND reciever_userid = '".$to_user_id."') 
					OR (sender_userid = '".$to_user_id."' 
					AND reciever_userid = '".$from_user_id."')
					OR broadcast LIKE '".$modcode."'
					UNION
					SELECT chat.*, '' as username, '' as surname 
					FROM ".$this->chatTable."
					WHERE broadcast LIKE '".$modcode."' ORDER BY timestamp ASC";
		}
			
		$userChat = $this->getData($sqlQuery);	
		$conversation = '<ul>';
		foreach($userChat as $chat){
			$user_name = '';
			if($chat["sender_userid"] == $from_user_id) {
				$conversation .= '<li class="replies">';
				$conversation .= '<img width="22px" height="22px" src="assets/images/'.$fromUserAvatar.'" alt="" />';	
			} else {
				$conversation .= '<li class="sent">';
				$conversation .= '<img width="22px" height="22px" src="assets/images/'.$toUserAvatar.'" alt="" />';		
			}
			if($chat["username"]){
				if(!is_numeric($to_user_id)){
				$conversation .= '<p><b>'.$chat["username"].' '.$chat["surname"].'</br></b>'.$chat["message"].'</p>';		
				}
				else{
					$conversation .= '<p>'.$chat["message"].'</p>';	
				}
			}
			else{
				$conversation .= '<p><b>'.$modcode.' Notification:</br></b>'.$chat["message"].'</p>';		
			}
			$conversation .= '</li>';
		}		
		$conversation .= '</ul>';
		return $conversation;
	}
	public function showUserChat($from_user_id, $to_user_id) {		
		$userDetails = $this->getUserDetails($to_user_id);
		$toUserAvatar = '';
		$title='';
		foreach ($userDetails as $user) {
			if(!is_numeric($user['user_id'])){$title=' Discussion';}
			$toUserAvatar = $user['avatar'];
			$userSection = '<div style="position: absolute; font-size:20px;"><img src="assets/images/'.$user['avatar'].'" alt="" />
				<p>'.$user['user_name'].' '.$user['user_surname'].$title.'</p></div>';
		}		
		// get user conversation
		$conversation = $this->getUserChat($from_user_id, $to_user_id);	
		// update chat user read status		
		$sqlUpdate = "
			UPDATE ".$this->chatTable." 
			SET status = '0' 
			WHERE sender_userid = '".$to_user_id."' AND reciever_userid = '".$from_user_id."' AND status = '1'";
		mysqli_query($this->dbConnect, $sqlUpdate);		
		// update users current chat session
		$sqlUserUpdate = "
			UPDATE ".$this->chatUsersTable."
			SET current_session = '".$to_user_id."' 
			WHERE user_id = '".$from_user_id."'";
		mysqli_query($this->dbConnect, $sqlUserUpdate);		
		$data = array(
			"userSection" => $userSection,
			"conversation" => $conversation			
		 );
		 echo json_encode($data);		
	}	
	public function getUnreadMessageCount($senderUserid, $recieverUserid) {
		$sqlQuery = "
			SELECT * FROM ".$this->chatTable."  
			WHERE sender_userid = '$senderUserid' AND reciever_userid = '$recieverUserid' AND status = '1'";
		$numRows = $this->getNumRows($sqlQuery);
		$output = '';
		if($numRows > 0){
			$output = $numRows;
		}
		return $output;
	}	
	public function updateTypingStatus($is_type, $loginDetailsId) {		
		$sqlUpdate = "
			UPDATE ".$this->chatLoginDetailsTable." 
			SET is_typing = '".$is_type."' 
			WHERE id = '".$loginDetailsId."'";
		mysqli_query($this->dbConnect, $sqlUpdate);
	}		
	public function fetchIsTypeStatus($userId){
		$sqlQuery = "
		SELECT is_typing FROM ".$this->chatLoginDetailsTable." 
		WHERE userid = '".$userId."' ORDER BY last_activity DESC LIMIT 1"; 
		$result =  $this->getData($sqlQuery);
		$output = '';
		foreach($result as $row) {
			if($row["is_typing"] == 'yes'){
				$output = ' - <small><em>Typing...</em></small>';
			}
		}
		return $output;
	}		
	public function insertUserLoginDetails($userId) {		
		$sqlInsert = "
			INSERT INTO ".$this->chatLoginDetailsTable."(userid) 
			VALUES ('".$userId."')";
		mysqli_query($this->dbConnect, $sqlInsert);
		$lastInsertId = mysqli_insert_id($this->dbConnect);
        return $lastInsertId;		
	}	
	public function updateLastActivity($loginDetailsId) {		
		$sqlUpdate = "
			UPDATE ".$this->chatLoginDetailsTable." 
			SET last_activity = now() 
			WHERE id = '".$loginDetailsId."'";
		mysqli_query($this->dbConnect, $sqlUpdate);
	}	
	public function getUserLastActivity($userId) {
		$sqlQuery = "
			SELECT last_activity FROM ".$this->chatLoginDetailsTable." 
			WHERE userid = '$userId' ORDER BY last_activity DESC LIMIT 1";
		$result =  $this->getData($sqlQuery);
		foreach($result as $row) {
			return $row['last_activity'];
		}
	}	
}
?>