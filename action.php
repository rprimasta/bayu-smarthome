<?php

//###########################database config##############################
$servername = "localhost";
$username = "safira";
$password = "r4h4s14";
$dbname = "safira";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
//print_r($_REQUEST);
//###########################Telegram Config##############################
$token = "551811703:AAGGnNaU8zrCf5LyYTokzJorWFAXpXToosg";
$botusername = "romishomebot";

switch($_REQUEST["mode"])
{
		case "post":
				//echo $_REQUEST["field"]."|".$_REQUEST["value"];
				post_value($_REQUEST["field"],$_REQUEST["value"],$conn); 
			break;
		case "get":
				get_value($_REQUEST["field"],$conn);
			break;
		case "getall":
				getall_value($conn);
				//print_r($_REQUEST);
			break;
		case "sync":
				getall_value($conn);
				$REQ = array();
				$REQ = $_GET;
				unset($REQ["mode"]);
				foreach ($REQ as $key => $value)
				{
					post_value_noret($key,$value,$conn); 
				}
			break;
		case "simpleSync":
				
				$REQ = array();
				$REQ = $_GET;
				unset($REQ["mode"]);
				foreach ($REQ as $key => $value)
				{
					post_value_noret($key,$value,$conn); 
				}

				simpleGetall_value($conn);
			break;
		case "teleGetUpdates":
				$result = file_get_contents("https://api.telegram.org/bot".$token."/getUpdates?offset=1000");
				echo($result);
				
			break;
		case "teleSendMessage":
				$msg = [
					'chat_id' => $_POST['chat_id'],//565132746,
					'text' => $_POST['text']//"Selamat Datang Rezza"
				];
				$req = "https://api.telegram.org/bot".$token."/sendMessage?" . http_build_query($msg);
				echo (file_get_contents($req));
				
				
			break;
		case "teleGetMe":
				$result = file_get_contents("https://api.telegram.org/bot".$token."/getMe");
				echo($result);
				
			break;
}

function post_value($field, $value, $connection){
	
	// Check connection
	if ($connection->connect_error) {

	    die("Connection failed: " . $connection->connect_error);
	} 

	$sql = "INSERT INTO `tbl_broker` (`FIELD`, `VALUE`, `TIMESTAMP`) VALUES ('".$field."', '". $value ."',CURRENT_TIMESTAMP);";
	//echo $sql; 
	
	if ($connection->query($sql) === TRUE) {
	    echo "{status:1}";
	} else {
	    echo "{status:-1}" . $connection->error;
	}
}

function post_value_noret($field, $value, $connection){
	
	// Check connection
	if ($connection->connect_error) {

	    die("Connection failed: " . $connection->connect_error);
	} 

	$sql = "INSERT INTO `tbl_broker` (`FIELD`, `VALUE`, `TIMESTAMP`) VALUES ('".$field."', '". $value ."',CURRENT_TIMESTAMP);";
	//echo $sql; 
	
	if ($connection->query($sql) === TRUE) {
	    //echo "{status:1}";
	} else {
	    //echo "{status:-1}" . $connection->error;
	}	
}

function get_value($field, $connection){
	
	// Check connection
	if ($connection->connect_error) {

	    die("Connection failed: " . $connection->connect_error);
	} 

	$sql = "SELECT * FROM `tbl_broker` WHERE FIELD='".$field."' ORDER BY ID DESC LIMIT 1";
	//echo $sql; 
	
	$result = $connection->query($sql);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "  {
						\"status\":1,
						\"field\":\"".$field."\",
						\"value\":\"".$row["VALUE"]."\"
					}";

		}
	} else {
		echo "{status:-1}";
	}
}

function getall_value($connection){
	
	// Check connection
	if ($connection->connect_error) {

	    die("Connection failed: " . $connection->connect_error);
	} 

	$sql = "SELECT *
				FROM tbl_broker
				WHERE tbl_broker.ID IN (
				SELECT MAX(tbl_broker.ID)
				FROM tbl_broker
				GROUP BY tbl_broker.FIELD
				)
				ORDER BY tbl_broker.FIELD ASC";
	//echo $sql; 
	
	$result = $connection->query($sql);
	$data = array();
	$res = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($data,$row);
		}
		$res["status"] = 1;
		$res["count"] = count($data);
		$res["data"] = $data;
		echo json_encode($res);
		
	} else {
		echo "{status:-1}";
	}
}

function simpleGetall_value($connection){
	
	// Check connection
	if ($connection->connect_error) {

	    die("Connection failed: " . $connection->connect_error);
	} 

	$sql = "SELECT *
				FROM tbl_broker
				WHERE tbl_broker.ID IN (
				SELECT MAX(tbl_broker.ID)
				FROM tbl_broker
				GROUP BY tbl_broker.FIELD
				)
				ORDER BY tbl_broker.FIELD ASC";
	//echo $sql; 
	
	$result = $connection->query($sql);
	$data = array();
	$res = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			//$temp = array();
			$res[$row["FIELD"]] = $row["VALUE"];
			//array_push($data,$temp);
		}
		$res["__status__"] = 1;
		//$res["__count__"] = count($data);
		//$res["data"] = $data;
		echo json_encode($res);
		
	} else {
		echo "{status:-1}";
	}	
}


$conn->close();
?>

