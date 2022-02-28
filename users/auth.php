<?php
	//error_reporting(0);
	/**************************************************************************************************
	****************Аутентификация пользователя********************************************************
	**************************************************************************************************/
	//define('WORK', true, true);
	//require_once ('../utils/requires.php');
	//print_r($_REQUEST);
	//exit();
	$username = pg_escape_string($_REQUEST["username"]);
	$password = $_REQUEST["password"];
	if (empty($username)){
		die(format_result("ERROR_EMPTY_USERNAME"));
	}
	if (empty($password)){
		die(format_result("ERROR_EMPTY_PASSWORD"));
	}
	$query = "SELECT * FROM tg_users WHERE username = '$username' OR email = '$username' OR phone = '$username' AND deleted = 0";
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	if (pg_num_rows($result) < 1) die(format_result('ERROR_USER_NOT_EXISTS'));
	if (pg_num_rows($result) > 1) die(format_result('ERROR_MULTIPLE_USERNAME'));
	//Проверяем пароль
	$user = pg_fetch_array($result);
	// Очистка результата
    pg_free_result($result);
	$user_password = md5($password . ':' . $user["salt"]);
	//echo $user_password.'|'.$user["password"];
	//print_r($user);
	if (trim($user["password"]) != trim($user_password)){
		die(format_result('ERROR_INVALID_PASSWORD'));
	}
	if ($user["banned"] != 0){
		die(format_array(array("Result"=>"ERROR_USER_BANNED", "Ban_reason"=>$user["ban_reason"], "Ban_end"=>$user["ban_end"])));
		//die('ERROR_USER_BANNED: ' . $user["ban_reason"] . ' : ' . $user["ban_end"]);
	}
	//Записываемся в историю авторизаций
	$ip = $_SERVER["REMOTE_ADDR"];
	$user_agent = $_SERVER["HTTP_USER_AGENT"];
	$filial_id = $user["filial_id"];
	$usr_id = $user["ID"];
	$query = "INSERT INTO tg_auth_history (\"date\", \"ip\", \"user_agent\", \"branch_id\", \"user_id\") VALUES (CURRENT_TIMESTAMP, '$ip', '$user_agent', $filial_id, $usr_id)";
	pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	//Пишем в сессию логин с паролем и выводим инфу о юзере
	session_start();
	$_SESSION["username"] = $username;
	$_SESSION["password"] = $password;
	//Получаем филиалы, в которых зарегистрировн пользователь
	$query = "SELECT * FROM tg_users_to_branches WHERE user_id = ".$user["ID"];
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	$user["branches"] = array();
	while ($branch = pg_fetch_array($result)){
		$query = "SELECT * FROM tg_branches WHERE \"ID\" = ".$branch["branch_id"];
		$r = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
		$br = pg_fetch_array($r);
		pg_free_result($r);
		$user["branches"][] = array("id"=>intval($branch["branch_id"]), "name"=>$br["name"]);
	}
	$_SESSION["user_id"] = $user["ID"];
	if ($user["is_superhost"] == 1){
		$user["is_superhost"] = true;
	}
	else{
		$user["is_superhost"] = false;
	}
	$user["rang_id"] = $user["rang"];
	$query = "SELECT * FROM tg_rang WHERE \"ID\" = ".$user["rang"];
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	$rang = pg_fetch_array($result);
	$user["rang"] = array("id"=>intval($user["rang"]), "name"=>trim($rang["name"]));
	$result = array();
	/*print_r($user);
	exit();*/
	$user["balance"] = str_replace('$', '', $user["balance"]);
	$user["balance"] = str_replace(',', '', $user["balance"]);
	$user["bonus"] = str_replace('$', '', $user["bonus"]);
	$user["bonus"] = str_replace(',', '', $user["bonus"]);
	if ($_SERVER["HTTP_USER_AGENT"] == 'Mozilla/3.0 (compatible) / Truegamers Admin Web Application'){
		$result["id"] = intval($user["ID"]);
		$result["username"] = $user["username"];
		$result["email"] = $user["email"];
		$result["phone"] = $user["phone"];
		$result["status"] = $user["status"];
		$result["is_superhost"] = $user["is_superhost"];
		$result["name"] = $user["name"];
		$result["surname"] = $user["surname"];
		$result["birthdate"] = date('Y-m-d H:i:s', strtotime($user["birthdate"]));
		$result["reg_date"] = date('Y-m-d H:i:s', strtotime($user["reg_date"])).$user["time_zone"];
		$result["last_visit"] = date('Y-m-d H:i:s', strtotime($user["last_visit"])).$user["time_zone"];
		$result["game_time_minutes"] = intval($user["game_time"]);
		$result["rang"] = $user["rang"];
		$result["balance"] = (float)$user["balance"];
		$result["bonus_balance"] = (float)$user["bonus"];
		$result["branches"] = $user["branches"];
	}
	else {
		$result["id"] = intval($user["ID"]);
		$result["username"] = $user["username"];
		$result["email"] = $user["email"];
		$result["phone"] = $user["phone"];
		$result["status"] = $user["status"];
		$result["is_superhost"] = $user["is_superhost"];
		$result["name"] = $user["name"];
		$result["surname"] = $user["surname"];
		$result["birthdate"] = date('Y-m-d H:i:s', strtotime($user["birthdate"]));
		$result["reg_date"] = date('Y-m-d H:i:s', strtotime($user["reg_date"])).$user["time_zone"];
		$result["last_visit"] = date('Y-m-d H:i:s', strtotime($user["last_visit"])).$user["time_zone"];
		$result["game_time_minutes"] = intval($user["game_time"]);
		$result["rang"] = $user["rang"];
		$result["balance"] = (float)$user["balance"];
		$result["bonus_balance"] = (float)$user["bonus"];
		$result["branches"] = $user["branches"];
	}
	$res["result"] = "RESULT_SUCCESS";
	$res["payload"] = $result;
	echo format_array($res);
?>
