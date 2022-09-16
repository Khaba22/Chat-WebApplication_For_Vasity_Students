<?php
include('../config/connection.php');

// User question
$user_input = strtolower($_GET['message']);

// Detect what is questions
$user_input = str_replace('what is', 'whatis', $user_input);
$user_input = str_replace('lecturer number', 'lecturernumber', $user_input);

// Split user question to find keywords
$input_array = str_replace(array("?"), '', $user_input);
$input_array = str_replace(array("'"), '', $user_input);
$input_array = explode(" ", $input_array);

// Question list from database
$query = "SELECT questions.question, questions.needs_query, responses.general_question, 
		  responses.context_query, responses.response_query FROM questions 
		  JOIN responses ON questions.general_responseid = responses.r_id";
$result= mysqli_query($dbc, $query);
$question_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

$match = false;
// Find out what the user is looking for and find out from who.. 
foreach($question_list as $i => $data){
	
	if($data['needs_query'] == 'yes') {
		// Match the 1st keyword of what the user is looking for
		if(in_array(strtolower($data['question']), $input_array)){
				$match = true;
				$context_query = $data['context_query'];
				$context_result = mysqli_query($dbc, $context_query);
				$context_list = mysqli_fetch_all($context_result, MYSQLI_ASSOC);
				
				foreach($context_list as $j => $data2){
					// Check grammar of 2nd keyword, ownershhip
					$condition1 = in_array(strtolower($data2['a']), $input_array); 		// normal
					$condition2 = in_array(strtolower($data2['a']."'"), $input_array); 	// appended '
					$condition3 = in_array(strtolower($data2['a']."s"), $input_array); 	// appended s
					$condition4 = in_array(strtolower($data2['a']."'s"), $input_array); // appended 's
					
					// Match the 2nd keyword of whom\what the user is looking for is from
					if($condition1 || $condition2 || $condition3 || $condition4){
						// Run response query to answer the user's question
						$response_query = $data['response_query'];
						$response_query = str_replace("***",$data2['a'],$response_query);
						$response_result = mysqli_query($dbc, $response_query);
						
						$response = "";
						while($response_data = mysqli_fetch_assoc($response_result)){
							$response  .= $response_data['a'];
							if(isset($response_data['b'])) {
								$response  .= ' '.$response_data['b'];
							}
							$response  .= ', ';
						}
						
						if(strlen($response) <> 0){					
							$response = substr($response, 0, strlen($response)-2);
							exit($response);
						} else{
							exit("Cannot be found!");
						}
					} 
				}
			} 
	} 	else if($data['needs_query'] == 'no') {
			$sql_query = "SELECT response_query FROM responses JOIN questions 
						  ON questions.general_responseid=responses.r_id WHERE question LIKE '{$user_input}'";
			
			if($sql_result= mysqli_query($dbc, $sql_query)){
				$response  = mysqli_fetch_assoc($sql_result);
				
				if($response){
					exit($response['response_query']);
				}												
			}					
		}
}

if($match) {
	exit('Your question did not match any records.');
} 	else {
		exit("Sorry, I did not understand you.");
	}
?>