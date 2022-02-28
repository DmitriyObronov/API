<?php
	error_reporting(0);
	/**************************************************************************************************
	*************************************Список пользователей******************************************
	**************************************************************************************************/
	/**************************************************************************************************
	 * Фильтрующие параметры:
	 *     q (текст) - поиск по части имени пользователя / телефона / email'a / имени / фамилии ("?" заменяет 1 произвольный символ, "*" заменяет строку произвольной длины)
	 *     id (число, список через запятую) - по ID пользователей
	 *     filial_id(число, список через запятую) - по id филиала
	 * Для сортировки укажите параметр order=
	 *     last_visit - по дате последнего визита
	 *     username, email, phone - по имени пользователя, email'у, телефону
	 * Ограничение количества элементов в выборке:
	 *     limit={количество в виде числа}
	 * Пропустить некоторое количество записей в начале:
	 *     offset={сколько пропустить в виде числа}
	 * Все параметры являются необзательными
	 *************************************************************************************************/
	define('WORK', true, true);
	require_once ('../utils/requires.php');
	//Проверяем, авторизован ли пользователь
	require_once("../users/authorise.php");
	$where = 'WHERE "deleted" = 0';
	if (intval($_GET["filial_id"]) > 0){
		$filial_id = pg_escape_string($_GET["filial_id"]);
		$where = "WHERE filial_id IN($filial_id)";
	}
	if (!empty($_GET["q"])){
		$q = pg_escape_string($_GET["q"]);
		$q = str_replace('*', '%', $q);
		$q = str_replace('?', '_', $q);
		$where .= " AND (username LIKE '%$q%' OR email LIKE '%$q%' OR phone LIKE '%$q%' OR \"name\" LIKE '%$q%' OR surname LIKE '%$q%')";
	}
	if (!empty($_GET["id"])){
		$id = pg_escape_string($_GET["id"]);
		$where .= " AND \"ID\" IN($id)";
	}
	if (!empty($_GET["order"])){
		$order = pg_escape_string($_GET["order"]);
		$where .= " ORDER BY $order";
	}
	if (!empty($_GET["limit"])){
		$limit = intval($_GET["limit"]);
		$where .= " LIMIT $limit";
	}
	if (!empty($_GET["offset"])){
		$offset = intval($_GET["offset"]);
		$where .= " OFFSET $offset";
	}
	$query = "SELECT * FROM tg_users ".$where;
	//echo $query;
	$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
	if (pg_num_rows($result) == 1){
	    $row = pg_fetch_array($result);
	    $row = exclude_indexes($row);
	    $res = format_array($row);
	}
	elseif (pg_num_rows($result) > 1){
	    $out = array();
    	while($row = pg_fetch_array($result)){
    		$out[] = $row;
    	}
    	$res = output_format_table($out);
	}
	else{
	    die(format_result("RESULT_NO_USERS_FOUND"));
	}
	die($res);
?>