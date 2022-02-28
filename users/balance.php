<?php
    /**************************************************************************************************
     *************************************Удаление пользователя****************************************
     **************************************************************************************************/
    /**************************************************************************************************
     * Принимаемые параметры:
     *     id (число) - ID удаляемого пользователя
     *************************************************************************************************/
    define('WORK', true, true);
    require_once ('../utils/requires.php');
    //Проверяем, авторизован ли пользователь
    $allowed_statuses = array(0, 1, 2, 3, 5);
    require_once("../users/authorise.php");
    $id = intval($_GET["id"]);
    if ($id < 1){
        die(format_result("ERROR_EMPTY_ID"));
    }
    $query = "SELECT * FROM tg_accounts_balance WHERE user_id = $id";
    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    if (pg_num_rows($result) < 1){
        die(format_result("ERROR_INVALID_USER"));
    }
    $usr = pg_fetch_array($result);
    $usr = exclude_indexes($usr);
    pg_free_result($result);
    $usr["Result"] = "RESULT_SUCCESS";
    echo format_array($usr);
?>