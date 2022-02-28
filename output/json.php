<?php
	if (! defined('WORK')) die('HIERARСHY_ERROR');
	/**************************************************************************************************
	****************Форматирование данных в формате JSON***********************************************
	**************************************************************************************************/
	//Вывод одиночного результата
	function format_result($data){
		if ($_SERVER["HTTP_X_CLIENT"] == "ClientShell"){
			return json_encode(array($data));
		}
		else{
			return json_encode(array("result"=>$data));
		}
	}

	//Вывод массива
	function format_array($arr, $caption = "General"){
		$data = array();
		$data = array_merge($data, $arr);
		return json_encode($data);
	}

	//Форматирование таблицы (двумерного массива) для вывода
	function output_format_table($tbl){
		$res = array("count"=>count($tbl));
		//Перебираем переданную таблицу построчно
		foreach ($tbl as $key=>$value){
			$row = exclude_indexes($value);//Исключаем индексные элементы
			$res[] = $row;
		}
		return json_encode($res);
	}

	//Вывод сообщение об ошибке запроса к БД
	function database_query_error($query, $file, $line){
		$msg = pg_last_error();
		$result = array('Result'=>'DATABASE_QUERY_ERROR', 'Error_message'=>$msg, 'Query'=>$query, 'File'=>$file, 'Line'=>$line);
		return json_encode($result);
	}
?>
