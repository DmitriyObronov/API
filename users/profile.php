<?php
  session_start();
  $usr_id = intval($_SESSION["user_id"]);
  $query = "SELECT * FROM \"tg_users\" WHERE \"ID\" = $usr_id";
  $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
  $usr = pg_fetch_array($result);
  //Получаем филиалы, в которых зарегистрировн пользователь
	$query = "SELECT * FROM tg_users_to_branches WHERE user_id = ".$usr["ID"];
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	$usr["branches"] = array();
	while ($branch = pg_fetch_array($result)){
    $query = "SELECT * FROM tg_branches WHERE \"ID\" = ".$branch["branch_id"];
		$r = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
		$br = pg_fetch_array($r);
		pg_free_result($r);
		$usr["branches"][] = array("id"=>intval($branch["branch_id"]), "name"=>$br["name"]);
	}
	$_SESSION["user_id"] = $usr["ID"];
	if ($usr["is_superhost"] == 1){
		$usr["is_superhost"] = true;
	}
	else{
		$usr["is_superhost"] = false;
	}
	$query = "SELECT * FROM tg_rang WHERE \"ID\" = ".$usr["rang"];
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	$rang = pg_fetch_array($result);
	$usr["rang"] = array("id"=>intval($usr["rang"]), "name"=>trim($rang["name"]));
	$result = array();
	/*print_r($usr);
	exit();*/
	$usr["balance"] = str_replace('$', '', $usr["balance"]);
	$usr["balance"] = str_replace(',', '', $usr["balance"]);
	$usr["bonus"] = str_replace('$', '', $usr["bonus"]);
	$usr["bonus"] = str_replace(',', '', $usr["bonus"]);
	if ($_SERVER["HTTP_USER_AGENT"] == 'Mozilla/3.0 (compatible) / Truegamers Admin Web Application'){
		$result["id"] = intval($usr["ID"]);
		$result["username"] = $usr["username"];
		$result["email"] = $usr["email"];
		$result["phone"] = $usr["phone"];
		$result["status"] = $usr["status"];
		$result["is_superhost"] = $usr["is_superhost"];
		$result["name"] = $usr["name"];
		$result["surname"] = $usr["surname"];
		$result["birthdate"] = date('Y-m-d H:i:s', strtotime($usr["birthdate"]));
		$result["reg_date"] = date('Y-m-d H:i:s', strtotime($usr["reg_date"])).$usr["time_zone"];
		$result["last_visit"] = date('Y-m-d H:i:s', strtotime($usr["last_visit"])).$usr["time_zone"];
		$result["game_time_minutes"] = intval($usr["game_time"]);
		$result["rang"] = $usr["rang"];
		$result["balance"] = (float)$usr["balance"];
		$result["bonus_balance"] = (float)$usr["bonus"];
		$result["branches"] = $usr["branches"];
	}
	else {
		$result["id"] = intval($usr["ID"]);
		$result["username"] = $usr["username"];
		$result["email"] = $usr["email"];
		$result["phone"] = $usr["phone"];
		$result["status"] = $usr["status"];
		$result["is_superhost"] = $usr["is_superhost"];
		$result["name"] = $usr["name"];
		$result["surname"] = $usr["surname"];
		$result["birthdate"] = date('Y-m-d H:i:s', strtotime($usr["birthdate"]));
		$result["reg_date"] = date('Y-m-d H:i:s', strtotime($usr["reg_date"])).$usr["time_zone"];
		$result["last_visit"] = date('Y-m-d H:i:s', strtotime($usr["last_visit"])).$usr["time_zone"];
		$result["game_time_minutes"] = intval($usr["game_time"]);
		$result["rang"] = $usr["rang"];
		$result["balance"] = (float)$usr["balance"];
		$result["bonus_balance"] = (float)$usr["bonus"];
		$result["branches"] = $usr["branches"];
	}
	$res["result"] = "RESULT_SUCCESS";
	$res["payload"] = $result;
	echo format_array($res);
?>
