<?php

namespace Klisl\Statistics\Controllers;


use App\Http\Controllers\Controller;
use Klisl\Statistics\Models\KslStatistic;
use Illuminate\Http\Request;


/**
 * Class StatController
 * Формирует страницу статистики
 *
 * @package Klisl\Statistics\Controllers
 */
class StatController extends Controller
{

    /**
     * @param array $condition
     * @param bool $stat_ip
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($condition = [], $stat_ip = false)
    {

        //Если доступ разрешен только аутентифицированным пользователям
        $auth_config = config('statistics.authentication');
        $user = \Auth::user();

        if ($auth_config && !$user) {
            $auth_route = config('statistics.auth_route');
            return redirect()->route($auth_route);
        }


        //Проверка доступа по вводу пароля
        $password_config = config('statistics.password');

        if ($password_config) {
            $session_stat = session('ksl-statistics');

            if (!$session_stat || ($session_stat !== $password_config)) {
                return view('Views::enter');
            }
        }


        $count_model = new KslStatistic(); //модель

        //Получение списка статистики
        $count_ip = $count_model->getCount($condition);

        //Преобразуем коллекцию к виду где более поздняя дата идет в начале
        $count_ip = $count_model->reverse($count_ip);



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


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function forms(Request $request){

        $count_model = $request->except('_token');

        /*
         * Форма входа на страницу статистики
         */
        if(isset($count_model['enter'])) {
            $password_config = config('statistics.password');
            $password_enter = $request->input('password');

            if ($password_config == $password_enter) {

                session(['ksl-statistics' => $password_config]);

                return redirect()->route('statistics');

            } else {
                session()->flash('error', 'Неверный пароль');
                return view('Views::enter');
            }
        }


        /*
         * Формы выбора параметров вывода статистики
         */
        $condition = [];
        $stat_ip = false;

        $model = new KslStatistic();


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

        return $this->index($condition, $stat_ip);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function enter(Request $request){
        $password_config = config('statistics.password');
        $password_enter = $request->input('password');

        if($password_config == $password_enter){

            session(['ksl-statistics' => $password_config]);

            return redirect()->route('statistics');

        } else {
            session()->flash('error', 'Неверный пароль');
            return view('Views::enter');
        }

    }
}
