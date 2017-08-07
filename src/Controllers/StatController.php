<?php

//namespace App\Http\Controllers;
namespace Klisl\Statistics\Controllers;

//use Yii;
//use yii\web\Controller;
//use common\modules\statistics\models\Count;
//use common\modules\statistics\models\Bot;

class StatController
{

    public function index()
    {
		exit('111');

		$count_model = new Count(); //модель Count
		$bot_model = new Bot(); //модель Bot
		
		$condition = [];
		$days_ago = null;
		$stat_ip = false;
		
		//Получение данных из формы для модели Count
		if ($count_model->load(Yii::$app->request->post())){
			
			//Сброс фильтров
			if($count_model->reset){
				$condition = [];
			}				
			//Вывод по дате
			if($count_model->date_ip){
				$timeUnix = strtotime($count_model->date_ip);
				$time_max = $timeUnix + 86400;
				//debug($count_model);
				
				$condition = ["between", "date_ip", $timeUnix , $time_max];				
			}
			//За период
			if($count_model->start_time){
				
				$timeStartUnix = strtotime($count_model->start_time);
				//Если не передана дата конца - ставим текущую
				if(!$count_model->stop_time) {
					$timeStopUnix = time();
				} else {
					$timeStopUnix = strtotime($count_model->stop_time);
				}
				$timeStopUnix += 86400; //целый день (до конца суток)
				$condition = ["between", "date_ip", $timeStartUnix , $timeStopUnix];	
			}
			//По IP
			if($count_model->ip){
				$condition = ["ip" => $count_model->ip];
				$days_ago = 86400 * 30; //за 30 дней
				$stat_ip = true;
			}
			//Добавить в черный список
			if($count_model->add_black_list){
				$count_model->set_black_list($count_model->ip, $count_model->comment);
				$condition = [];
				$days_ago = null;
			}
			//Удалить из черного списка
			if($count_model->del_black_list){
				$count_model->remove_black_list($count_model->ip);
				$condition = [];
				$days_ago = null;
			}		
			//Удалить старые данные
			if($count_model->del_old){
				$count_model->remove_old();
				Yii::$app->end();  //PJAX
			}			
			$count_model = new Count(); //новый объект модели для очистки формы
		}
		
//		//Статистика по поисковым ботам
//		if ($bot_model->load(Yii::$app->request->post())){
//			if($bot_model->get_bot_stat){
//				$bot_model->by_bot();
//				Yii::$app->end();  //PJAX
//			}
//		}

		//Получение списка статистики
		$count_ip = $count_model->getCount($condition, $days_ago);
		/*
		 * Устанавливаем значение полей по-умолчанию для вывода в полях формы
		 */
		$count_model->date_ip = time(); //сегодня
		$count_model->start_time = date('Y-m-01'); //первое число текущего месяца
		$count_model->stop_time = time(); //сегодня
				
        return $this->render('index',[
			'count_model'=> $count_model,			
//			'bot_model'=> $bot_model,
			'count_ip'=> $count_ip, //статистика
			'stat_ip' => $stat_ip, //true если фильтр по определенному IP
		]);
    }
}
