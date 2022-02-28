<?php
    define("EMAIL_PATTERN", '#[-_0-9a-zа-я\.]{3,}@[-_0-9a-zа-я\.]{2,}\.[0-9a-zа-я]{2,}#isU', true);
    define("PHONE_PATTERN", "#^\+[0-9]{11, 14}$#isU", true);
    define("DATETIME_PATTERN", "#[0-9]{4}\.[0-9]{2}\.[0-9]{2}\|[0-9]{2}:[0-9]{2}:[0-9]{2}#isU", true);
    define("IP_PATTERN", "#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#isU", true);
	if (! defined('WORK')) die('HIERARСHY_ERROR');
	/**************************************************************************************************
	***********************************Различные функции***********************************************
	**************************************************************************************************/
	//Генерация паролей и токенов
	function generate_password($symbols_count = 10, $small_letters = true, $big_letters = true, $numbers = true, $symbols = true){
		$smb = '';
		if ($small_letters){
			$smb .= 'abcdefghijklmnopqrstuvwxyz';
		}
		if ($big_letters){
			$smb .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		if ($numbers){
			$smb .= '0123456789';
		}
		if ($symbols){
			$smb .= '!|\\/?!*^&:;><%$#@~-_+=';
		}
		$smb = str_split($smb);
		$res = '';
		for ($i = 0; $i < $symbols_count; $i++){
			$rnd = rand(0, count($smb) - 1);
			$res .= $smb[$rnd];
		}
		return $res;
	}
	//Проверка, соответствует ли запись в таблице указанному филиалу
	function check_filial_id($tb_name, $record_id, $filial_id){
		$query = "SELECT * FROM \"$tb_name\" WHERE \"ID\" = $record_id";
		$result = pg_query($query) or die(database_query_error($query, __FILE__, __LINE__));
		$record = pg_fetch_array($result);
		return $record["filial_id"] = $filial_id;
	}
	//нормализуем дату/время
	function normalize_datetime($datetime){
	    $datetime = str_replace('|', ' ', $datetime);
	    $datetime = str_replace('.', '-', $datetime);
	    return $datetime;
	}
	function check_ip($ip){
	    if (!preg_match(IP_PATTERN, $ip)){
	        return false;
	    }
	    else{
	        $arr = explode('.', $ip);
	        $result = true;
	        foreach ($arr as $value){
	            if ($value > 255){
	                $result = false;
	                break;
	            }
	        }
	        return $result;
	    }
	}
  //Проверяем корректность денежной суммы
  function check_money($sum){
    $pattern = "#^([0-9]+)|([0-9]+\.[0-9]{1, 2})|([0-9]+,[0-9]{1, 2})$#isU";
    if (!preg_match($pattern, $sum)){
      return false;
    }
    else{
      return str_replace(',', '.', $sum);
    }
    /*if (strpos($sum, '.')){
      $items = explode('.', $sum);
      $pattern = "#^[0-9]{1, 8}$#is";
      if (preg_match($pattern, $items[0])){
        $pattern = "#^[0-9]{1, 2}$#is";
        if (preg_match($pattern, $items[1])){
          return $items[0].".".$items[1];
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }
    elseif (strpos($sum, ',')){
      $items = explode(',', $sum);
      $pattern = "#^[0-9]{1, 8}$#is";
      if (preg_match($pattern, $items[0])){
        $pattern = "#^[0-9]{1, 2}$#is";
        if (preg_match($pattern, $items[1])){
          return $items[0].".".$items[1];
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }
    elseif (intval($sum) > 0){
      return $sum;
    }
    else {
      return false;
    }*/
  }
  function send_sms($phone, $text){

  }
  function send_email($email, $from, $subject, $content){

    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: $from\r\n";
    $headers .= "Reply-to: $from\r\n";
    $headers .= "X-Mailer: PHP/".phpversion()."\r\n\r\n";

    mail($email, $subject, $content, $headers);
  }
?>
