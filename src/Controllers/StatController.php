<?php

//namespace App\Http\Controllers;
namespace Klisl\Statistics\Controllers;


use Klisl\Statistics\Models\KslStatistic;
use Illuminate\Http\Request;



class StatController
{

    public function index($condition = [], $days_ago = null, $stat_ip=false)
    {
//        dd($condition);

//		exit('111');

		$count_model = new KslStatistic(); //модель Count
//		$bot_model = new Bot(); //модель Bot
		
//		$condition = [];
//		$days_ago = null;
//		$stat_ip = false;
		
		//Получение данных из формы для модели Count
//		if ($count_model->load(Yii::$app->request->post())){

		
//		//Статистика по поисковым ботам
//		if ($bot_model->load(Yii::$app->request->post())){
//			if($bot_model->get_bot_stat){
//				$bot_model->by_bot();
//				Yii::$app->end();  //PJAX
//			}
//		}

		//Получение списка статистики
		$count_ip = $count_model->getCount($condition, $days_ago);

//		dd($days_ago);

		/*
		 * Устанавливаем значение полей по-умолчанию для вывода в полях формы
		 */
		$count_model->date_ip = time(); //сегодня
		$count_model->start_time = date('Y-m-01'); //первое число текущего месяца
		$count_model->stop_time = time(); //сегодня
				
//        return $this->render('index',[
//			'count_model'=> $count_model,
////			'bot_model'=> $bot_model,
//			'count_ip'=> $count_ip, //статистика
//			'stat_ip' => $stat_ip, //true если фильтр по определенному IP
//		]);
        $black_list = $count_model->count_black_list();


        return view('Views::index',[
            'count_model'=> $count_model,
			'count_ip'=> $count_ip, //статистика
			'stat_ip' => $stat_ip, //true если фильтр по определенному IP
            'black_list' => $black_list
        ]);
    }


    //Проверяет, является ли посетитель роботом поисковой системы.
    public function isBot(&$botname = ''){
        $bots = array(
            'rambler','googlebot','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
            'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
            'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
            'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
            'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
            'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
            'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
            'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
            'Nigma.ru','bing.com','dotnetdotcom'
        );
        foreach($bots as $bot)
            if(stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false){
                $botname = $bot;
                return $botname;
            }
        return false;
    }


    public function forms(Request $request){

        $condition = [];
		$days_ago = null;
//        $days_ago = time() - (86400 * self::STAT_DEFAUL) - $sec_todey;

        $stat_ip = false;

        $model = new KslStatistic();

            $count_model = $request->except('_token');
//            dd($count_model);
            //Сброс фильтров
			if(isset($count_model['reset'])){
				$condition = [];
			}

            if(isset($count_model['date_ip'])){

                $time = strtotime($count_model['date_ip']);
//                $time = $count_model['date_ip'];


                $time_max = $time + 86400;
//                $time_max = date("Y-m-d",time() + 86400);

                $condition = ["created_at", $time , $time_max];
            }



            //За период
            if(isset($count_model['period'])){

                if(isset($count_model['start_time'])){
                    $timeStartUnix = strtotime($count_model['start_time']);
                } else {
                    $timeStartUnix = 0;
                }

                //Если не передана дата конца - ставим текущую
                if(!isset($count_model['stop_time'])) {
                    $timeStopUnix = time();
                } else {
                    $timeStopUnix = strtotime($count_model['stop_time']);
                }

                $timeStopUnix += 86400; //целый день (до конца суток)
                $condition = ["created_at", $timeStartUnix , $timeStopUnix];
            }




            //По IP
            if(isset($count_model['search_ip'])){

                $condition = ["ip" => $count_model['ip']];
                $stat_ip = true;

                if(!$count_model['ip']) session()->flash('error', 'Укажите IP для поиска');
            }


            //Добавить в черный список
            if(isset($count_model['ip'])){

                if(!isset($count_model['comment'])) $count_model['comment'] ='';
                $model->set_black_list($count_model['ip'], $count_model['comment']);
            }

            //Удалить из черного списка
            if(isset($count_model['del_black_list'])){
                $model->remove_black_list($count_model['ip']);
            }

            //Удалить старые данные
            if(isset($count_model['del_old'])){
                $model->remove_old();
            }

        return $this->index($condition, $days_ago, $stat_ip);
    }
}
