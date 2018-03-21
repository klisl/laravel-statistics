<?php

namespace Klisl\Statistics\Controllers;

use Klisl\Statistics\Models\KslStatistic;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


/**
 * Формирует страницу статистики
 *
 * @package Klisl\Statistics\Controllers
 */
class StatController extends BaseController
{

    /**
     * Отвечает за вывод страницы статистики
     *
     * @param array $condition
     * @param bool $stat_ip
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($condition = [], $stat_ip = false)
    {

        $this->checkAccess(); //проверка доступа к странице

        $checkPassword = $this->checkPassword(); //проверка ввода пароля
        if(!$checkPassword){
            return redirect()->route('enter');
        }

        $count_model = new KslStatistic(); //модель

        //Получение списка статистики
        $count_ip = $count_model->getCount($condition);

        //Преобразуем коллекцию к виду где более поздняя дата идет в начале
        $count_ip = $count_model->reverse($count_ip);


		//Значение полей по-умолчанию для вывода в полях формы
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
     * Проверка доступа пользователя к просмотру страницы статистики
     * перенаправление на страницу входа если не авторизован
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkAccess(){

        $auth_config = config('statistics.authentication');
        $user = \Auth::user();

        if ($auth_config && !$user) {
            $auth_route = config('statistics.auth_route');
            redirect()->route($auth_route)->send();
        }
    }


    /**
     * Проверка пароля сохраненного в сессии для доступа к странице статистики
     *
     * @return bool
     */
    public function checkPassword(){

        $password_config = config('statistics.password');

        if ($password_config) {
            $session_stat = session('ksl-statistics');

            if ($session_stat !== $password_config) {
                return false;
            }
        }
        return true;
    }



    /**
     * Обработка форм - форма входа и формы со страницы статистики
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function forms(Request $request){

        $count_model = $request->except('_token');

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


}
