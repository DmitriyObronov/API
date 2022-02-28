<?php
    /**************************************************************************************************
     *************************************Загрузка клуба***********************************************
     **************************************************************************************************/
    $date = $_REQUEST["date"];
    $date = str_replace('|', ' ', $date);
    $branch_id = intval($_REQUEST["branch_id"]);
    $query = "SELECT SUM(duration) AS duration FROM tg_game_time WHERE '$date'::date = date_start::date AND filial_id = $filial_id AND date_end <= '$date'";
    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    $res = pg_fetch_array($result);
    $now = strtotime($date);
    $diff = ($now - strtotime('today')) / 60;
    //echo $diff;
    $query = "SELECT COUNT(*) as cnt FROM tg_club_map WHERE filial_id = $filial_id";
    $result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
    $computers = pg_fetch_array($result);
    $cnt = $computers["cnt"];
    $diff = $cnt * $diff;
    exit(round(($res["duration"] / $diff) * 100));
?>
