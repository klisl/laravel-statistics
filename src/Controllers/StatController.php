<?php

//namespace App\Http\Controllers;
namespace Klisl\Statistics\Controllers;


use Klisl\Statistics\Models\KslStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class StatController
{

    public function index($condition = [], $days_ago = null, $stat_ip = false)
    {

        //Проверка доступа по вводу пароля
        $session_stat = session('ksl-statistics');
        $enter = Input::get('ksl-statistics');


        dd($enter);
        if(!$session_stat){
            return view('Views::enter',[

            ]);
        }
//        dd($session_stat);


		$count_model = new KslStatistic(); //модель

		//Получение списка статистики
		$count_ip = $count_model->getCount($condition, $days_ago);

		/*
		 * Устанавливаем значение полей по-умолчанию для вывода в полях формы
		 */
		$count_model->date_ip = time(); //сегодня
		$count_model->start_time = date('Y-m-01'); //первое число текущего месяца
		$count_model->stop_time = time(); //сегодня

        $black_list = $count_model->count_black_list();


        return view('Views::index',[
			'count_ip'=> $count_ip, //статистика
			'stat_ip' => $stat_ip, //true если фильтр по определенному IP
            'black_list' => $black_list,
        ]);
    }





    public function forms(Request $request){

        $condition = [];
		$days_ago = null;
        $stat_ip = false;

        $model = new KslStatistic();

        $count_model = $request->except('_token');

        //Сброс фильтров
        if(isset($count_model['reset'])){
            $condition = [];
        }

        if(isset($count_model['date_ip'])){
            $time = strtotime($count_model['date_ip']);
            $time_max = $time + 86400;
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
        if(isset($count_model['add_black_list'])){

            if(!$count_model['ip']){
                session()->flash('error', 'Укажите IP для добавления в черный список');
            } else {
                $ip = $request->only('ip');
//                dd($ip);
                $rules = [
                    'ip'=>'ip',
                ];
                $validator = \Validator::make($ip, $rules);
                if ($validator->fails()) {
                    session()->flash('error', 'Указан неправильный IP');
                } else {
                    if(!isset($count_model['comment'])) $count_model['comment'] ='';
                    $model->set_black_list($count_model['ip'], $count_model['comment']);
                }
            }
        }

        //Удалить из черного списка
        if(isset($count_model['del_black_list'])){

            if(!$count_model['ip']){
                session()->flash('error', 'Укажите IP для удаления из черного списка');
            } else {
                $model->remove_black_list($count_model['ip']);
            }
        }

        //Удалить старые данные
        if(isset($count_model['del_old'])){
            $model->remove_old();
        }

        return $this->index($condition, $days_ago, $stat_ip);
    }


    public function enter(Request $request){
        $password_config = config('statistics.password');
        $password_enter = $request->input('password');
//        dd($password_enter);
        if($password_config == $password_enter){
//            session(['ksl-statistics' => true]);
            $cookie = cookie('ksl-statistics', 'ksl', 12*60);
            return redirect()->route('statistics')->withCookie($cookie);;
        } else {
            session()->flash('error', 'Неверный пароль');
            return view('Views::enter');
        }

    }
}
