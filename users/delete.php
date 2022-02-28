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
    $allowed_statuses = array(0, 1, 2, 3);
    require_once("../users/authorise.php");
    $id = intval($_GET["id"]);
    if ($id < 1){
        die(format_result("ERROR_EMPTY_ID"));
    }
    $query = "SELECT * FROM \"users\" WHERE \"ID\" = $id";
    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    if (pg_num_rows($result) < 1){
        die(format_result("ERROR_USER_NOT_EXISTS"));
    }
    $usr = pg_fetch_array($result);
    if (!check_user_access_rights($usr)){
        die(format_result("ERROR_PERMISSION_DENIED"));
    }
    $query = "UPDATE tg_users SET deleted = 1 WHERE \"ID\" = $id";
    pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    exit(format_result("RESULT_SUCCESS"));
?>