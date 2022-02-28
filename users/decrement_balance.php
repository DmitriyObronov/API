<?php
    /**************************************************************************************************
     ************************Выведение баланса пользователя********************************************
     **************************************************************************************************/
    /**************************************************************************************************
     * Принимаемые параметры:
     *     id (число) - ID пользователя
     *     balance (число с плавающей точкой, например 230.16) - баланс пользователя
     *************************************************************************************************/
    //Проверяем, авторизован ли пользователь
    $id = intval($_REQUEST["id"]);
    if ($id > 1){
        //проверяем права доступа у текущего пользователя - он не должен быть простым пользователем
    }
    else {
      $id = $user["ID"];
    }
    if (empty($_REQUEST["sum"])){
      die(format_result("ERROR_EMPTY_SUM"));
    }
    $sum = pg_escape_string($_REQUEST["sum"]);
    //Проверяем сумму на корректность
    $sum = check_money($sum);
    if (!$sum){
      die(format_result("ERROR_INVALID_SUM"));
    }
    $action = $_REQUEST["action"];
    if (empty($action)){
      die(format_result("ERROR_EMPTY_ACTION"));
    }
    if ($action != 'decrement_balance' && $action != 'decrement_bonus'){
      die(format_result("ERROR_INVALID_ACTION"));
    }
    if ($action == 'decrement_balance'){
      //Проверяем, можем ли мы списать с баланса
      $query = "SELECT \"balance\" FROM tg_users WHERE \"ID\" = $id";
      $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
      $usr = pg_fetch_array($result);
      $balance = $usr["balance"];
      $balance = (float)str_replace(array(",", '$'), "", $balance);
      if ($balance < $sum){
        die(format_result("ERROR_BALANCE_NOT_ENOUGH"));
      }
      $query = "UPDATE tg_users SET \"balance\" = \"balance\" - '$sum'::money WHERE \"ID\" = $id";
    }
    else {
      //Проверяем, можем ли мы списать с баланса
      $query = "SELECT \"bonus\" FROM tg_users WHERE \"ID\" = $id";
      $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
      $usr = pg_fetch_array($result);
      $bonus = $usr["bonus"];
      $bonus = (float)str_replace(array(",", '$'), "", $bonus);
      if ($bonus < $sum){
        die(format_result("ERROR_BONUSES_NOT_ENOUGH"));
      }
      $query = "UPDATE tg_users SET \"bonus\" = \"bonus\" - '$sum'::money WHERE \"ID\" = $id";
    }
    pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    $query = "SELECT * FROM tg_users WHERE \"ID\" = $id";
    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    $usr = pg_fetch_array($result);
    pg_free_result($result);
    $result = array();
    $result["id"] = $user["ID"];
    $result["balance"] = (float)str_replace(array(",", '$'), "", $usr["balance"]);
		$result["bonus_balance"] = (float)str_replace(array(",", '$'), "", $usr["bonus"]);
    $res["result"] = "RESULT_SUCCESS";
  	$res["payload"] = $result;
  	echo format_array($res);
    exit();
?>
