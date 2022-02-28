<?php
	/**************************************************************************************************
	***************************Создание пользователя***************************************************
	**************************************************************************************************/
	/**************************************************************************************************
	 * Обязательные параметры:
	 *     username (строка) - Логин
	 *     password (строка) - Пароль
	 *     email (строка) - email
	 *     phone (строка) - номер телефона в формате +79999999999
	 *     filial_id (число) - ID филиала из таблицы из tg_filial (доступно только суперхосту и разработчику)
	 * Необязательные параметры:
	 *     name, surname (строка) - Имя и фамилия
	 *     birthdate (дата в формате yyyy.mm.dd|hh:mm:ss) - дата рождения
	 *
	 *************************************************************************************************/
	define('WORK', true, true);
	require_once ('../utils/requires.php');
	//Проверяем, авторизован ли пользователь
	$allowed_statuses = array(0, 1, 2, 3);
	require_once("../users/authorise.php");
	//Чекаем необходимые параметры
	$filial_id = intval($_REQUEST["filial_id"]);
	require_once '../utils/settings.php';
	if ($filial_id < 1){
	    die(format_result("ERROR_EMPTY_FILIAL_ID"));
	}
	if ($user["status"] != 0 && $user["status"] != 2){
	    if ($user["filial_id"] != $filial_id){
	        die(format_result("ERROR_USER_EXCEEDED_POWERS"));
	    }
	}
	$username = pg_escape_string($_REQUEST["username"]);
	$email = pg_escape_string($_REQUEST["email"]);
	$phone = pg_escape_string($_REQUEST["phone"]);
	$password = pg_escape_string($_REQUEST["password"]);
	if (empty($username)){
	    die(format_result("ERROR_EMPTY_USERNAME"));
	}
	else{
	    $query = "SELECT * FROM tg_users WHERE filial_id = $filial_id AND username = '$username'";
	    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	    if (pg_num_rows($result) > 0){
	        die(format_result("ERROR_USERNAME_IS_BUSY"));
	    }
	}
	if (empty($email)){
	    die(format_result("ERROR_EMPTY_EMAIL"));
	}
	else{
	    if (!preg_match(EMAIL_PATTERN, $email)){
	        die(format_result("ERROR_INVALID_EMAIL"));
	    }
	    $query = "SELECT * FROM tg_users WHERE filial_id = $filial_id AND email = '$email'";
	    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	    if (pg_num_rows($result) > 0){
	        die(format_result("ERROR_EMAIL_IS_BUSY"));
	    }
	}
	if (empty($phone)){
	    die(format_result("ERROR_EMPTY_PHONE"));
	}
	else{
	    /*if (!preg_match(PHONE_PATTERN, $phone)){
	        die(format_result("ERROR_INVALID_PHONE"));
	    }*/
	    $query = "SELECT * FROM tg_users WHERE filial_id = $filial_id AND phone = '$phone'";
	    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	    if (pg_num_rows($result) > 0){
	        die(format_result("ERROR_PHONE_IS_BUSY"));
	    }
	}
	if (empty($password)){
	    die(format_result("ERROR_EMPTY_PASSWORD"));
	}
	$birthdate = $_REQUEST["birthdate"];
	if (empty($birthdate)){
	    $birthdate = 'NULL';
	}
	else{
	    if (!preg_match(DATETIME_PATTERN, $birthdate)){
	        die(format_result("ERROR_INVALID_BIRTHDATE"));
	    }
	    $birthdate = "'".normalize_datetime($birthdate)."'";
	}
	//$birthdate = '1980-04-30 00:00:00';
	$name = pg_escape_string($_REQUEST["name"]);
	$surname = pg_escape_string($_REQUEST["surname"]);
	$salt = generate_password();
	$pass = md5($password.":".$salt);
	$email_token = "";
	if ($settings["registration_check_email"]["int_val"]){
	    $email_token = generate_password(100);
	}
	$phone_code = 0;
	if ($settings["registration_check_phone"]["int_val"]){
	    $phone_code = generate_password(5, false, false, true, false);
	}
	else {
	    $phone_code = 'NULL';
	}
	$query = "INSERT INTO tg_users (username, password, salt, email, phone, name, surname, birthdate, reg_date, last_visit, phone_code, email_token, filial_id) VALUES(";
	$query .= "'$username', '$pass', '$salt', '$email', '$phone', '$name', '$surname', '$birthdate', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, $phone_code, '$email_token', $filial_id)";
	pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	//Получаем данные о вновь созданном пользователе
	$query = "SELECT * FROM tg_users ORDER BY \"ID\" DESC LIMIT 1";
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	$usr = pg_fetch_array($result);
	$usr = exclude_indexes($usr);
	pg_free_result($result);
	$last_insert_id = $usr["ID"];
	$query = "INSERT INTO tg_accounts_balance (user_id, money, bonus, filial_id) VALUES($last_insert_id, '0.00', '0.00', $filial_id)";
	pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	$usr["Result"] = "RESULT_SUCCESS";
	echo format_array($usr);
	if (intval($_GET["test"]) == 1){
	    $query = "DELETE FROM tg_users WHERE \"ID\" = $last_insert_id";
	    pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	}
?>
