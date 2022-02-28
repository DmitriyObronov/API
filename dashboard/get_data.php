<?php
  //Массивы со списком полей
  $total_sum_fields = array("cash", "card", "bar_cash", "bar_card", "acquiring"); //Полный доход клуба
  if (!empty($_GET["filter"])){
    $filter = $_GET["filter"];
    $filter = json_decode($filter, true);
  }
  $data = array();//Массив для всех филиалов
  $total_data = array(); //Данные по всем выводимым клубам
  foreach ($filter as $branch_id){
    $total_sum_flds = implode(" + ", $total_sum_fields);
    $fields = implode(", ", $total_sum_fields);
    $query = "SELECT $total_sum_flds, $fields FROM tg_kassa_smena WHERE branch_id = $branch_id AND ";
  }
?>
