<?php
/*
 * Сохраняет в БД IP посетителя
 */
namespace common\modules\statistics;

use Yii;
use common\modules\statistics\models\Count;
use common\modules\statistics\models\Bot;

	class CountKsl
{
	static function init(){
		$ip = Yii::$app->request->userIP; //получаем IP текущего посетителя

		$count_model = new Count(); //модель Count
		$bot_model = new Bot(); //модель Bot

		$str_url =  "http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]; //URL текущей страницы

		//Проверка на бота
		$bot_name = $bot_model->isBot();
		//$bot_name = 'rambler'; //для тестирования

		if($bot_name){
			$bot_model->set_stat_bot($bot_name,$str_url,$ip);
		} else {
			//Проверка в черном списке
			$black = $count_model->inspection_black_list($ip);
			if(!$black){
				$count_model->setCount($ip, $str_url, 0);
			}
		}
	}
}