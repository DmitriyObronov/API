<?php
  //ini_set("display_errors", 1);
  //Разрешаем кроссдоменный JavaScript
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin,Content-Type,Accept,X-Requested-With');
  //Давим кэширование
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Cache-Control: post-check=0,pre-check=0", false);
  header("Cache-Control: max-age=0", false);
  header("Pragma: no-cache");
  print_r($GLOBALS);
  //Начинаем работу
  define('WORK', true, true);
  require_once ('utils/requires.php');
  $route = str_replace('/api/v1.1/', '', $_SERVER["REQUEST_URI"]);
  $arr = explode('/', $route);
  unset($_GET["route"]);
  $arr[0] = str_replace(array($_SERVER["QUERY_STRING"], "?"), "", $arr[0]);
  if (strpos($arr[0], "?") > 0){
    $buf = explode("?", $arr[0]);
    $arr[0] = $buf[0];
  }
  if (strpos($arr[1], "?") > 0){
    $buf = explode("?", $arr[1]);
    $arr[1] = $buf[0];
  }
  if ($arr[0] == 'login'){
    //Аутентификация пользователя
    require_once('users/auth.php');
  }
  elseif ($arr[0] == "users" && $arr[1] == "register"){
    require_once('users/register.php');
  }
  elseif ($arr[0] == "users" && $arr[1] == "admin_auth"){//Авторизация администратора клуба и отправка файла коннекта к БД
    require_once('users/admin_auth.php');
  }
  elseif ($arr[0] == "branch" && $arr[1] == "admins"){ //Список админов клуба для админской шелки
    require_once('branch/admins.php');
  }
  else {
    //Проверяем, авторизован ли пользователь
    require_once('users/authorise.php');

    //Выводим профиль пользователя

    if ($arr[0] == 'profile'){
      require_once('users/profile.php');
    }

    //Дашборд

    if ($arr[0] == 'dashboard'){
      //Выводим информацию по дашборду
      require_once('dashboard/get_data.php');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    //Клиентская шелка
    ///////////////////////////////////////////////////////////////////////////////////////////

    //Тарифы

    if ($arr[0] == 'prices'){

      //Список тарифов для конкретного компа (07.01.2021)
      if ($arr[1] == 'list-for-category'){
        require_once('prices/list-for-category.php');
      }

    }

    //Игры

    if ($arr[0] == 'games'){

      //Список игр для конкретного компа (10.01.2021)
      if ($arr[1] == 'list'){
        require_once('games/list.php');
      }

    }

    //Пользователи

    if ($arr[0] == 'users'){

      //Уменьшение баланса или бонусов конкретного пользователя
      if ($arr[1] == 'decrement-balance'){
        require_once('users/decrement_balance.php');
      }

    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    //Админская шелка
    ///////////////////////////////////////////////////////////////////////////////////////////

    //Филиалы

    if ($arr[0] == "branch"){

      //Загрузка клуба
      if ($arr[1] == "occupancy"){
        require_once('branch/club_occupancy.php');
      }

    }

  }
?>
