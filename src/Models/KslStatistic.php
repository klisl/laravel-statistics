<?php

namespace Klisl\Statistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


/**
 * Class KslStatistic
 * @package Klisl\Statistics\Models
 * @mixin \Illuminate\Database\Query\Builder
 */
class KslStatistic extends Model{

    /**
     * Название таблицы
     * @var string
     */
    protected $table = 'kslStatistics';


    /**
     * Проверка наличия IP в черном списке (которые не надо выводить и сохранять в БД)
     * если есть хоть одна строка, то вернет true
     *
     * @param string $ip
     * @return bool
     */
    public function inspection_black_list($ip){
        $check = $this
            ->where('ip', $ip)
            ->where('black_list_ip', 1)
            ->get();

        if (!$check->isEmpty()) return true;

        return false;
    }

    /**
     * @param string $ip
     * @param string $str_url
     * @param int $black_list_ip
     * @return void
     */
    public function setCount($ip, $str_url, $black_list_ip = 0){
        $this->ip = $ip;
        $this->str_url = $str_url;
        $this->black_list_ip = $black_list_ip;
        $this->save();
    }


    /**
     * @param array|null $condition
     * @return Collection
     */
    public function getCount($condition = null){

        $sec_todey = time() - strtotime('today'); //сколько секунд прошло с начала дня

        //за сколько дней показывать по-умолчанию
        $days_show_stat = config('statistics.days_default') -1 ;

        $date_unix = $days_ago = time() - (86400 * $days_show_stat) - $sec_todey;
        //В формат 2017-08-05 00:00:00 как в БД
        $days_ago = date("Y-m-d H:i:s",$date_unix);

        //Выбор диапазона между двумя датами
        if(in_array( 'created_at',$condition)) {
            $count_ip = $this
                ->where('black_list_ip', '<', 1)
                ->whereBetween($condition[0], [date("Y-m-d H:i:s",$condition[1]), date("Y-m-d H:i:s",$condition[2])])
                ->orderBy('created_at')
                ->get();

        } elseif($condition){

            $count_ip = $this
                ->where('black_list_ip', '<', 1)
                ->where('created_at', '>', $days_ago)
                ->where('ip', $condition)
                ->orderBy('created_at')
                ->get();

        } else {
            $count_ip = $this
                ->where('black_list_ip', '<', 1)
                ->where('created_at', '>', $days_ago)
                ->orderBy('created_at')
                ->get();
        }

        return $count_ip;
    }

    /**
     * Выборка номеров IP которые в черном списке
     *
     * @return Collection
     */
    public function count_black_list(){

            $black_list = $this
            ->select('ip')
            ->where('black_list_ip', 1)
            ->distinct() //уникальные значения
            ->get();

        //По полученному массиву IP получаем значение ячейки "comment"
        foreach ($black_list as $key => $arr){
            $rez = $arr->where(['ip' => $arr['ip']])->first();
            $black_list[$key]['comment'] = $rez->comment;
        }

        return $black_list;
    }


    /**
     * Добавление в черный список
     *
     * @param string $ip
     * @param string $comment
     * @return void
     */
    public function set_black_list($ip, $comment=''){
        $res = false;
        $verify_black_list = $this->where('ip', $ip)->get();

        //Если такой IP уже есть (коллекция не пуста)
        if(!$verify_black_list->isEmpty()){
            foreach ($verify_black_list as $str){
                $str->black_list_ip = 1;
                $str->comment = $comment;
                $res = $str->save();
            }
        } else {
            $this->ip = $ip;
            $this->str_url = '';
            $this->black_list_ip = 1;
            $this->comment = $comment;
            $res = $this->save();
        }

        if($res) session()->flash('status', 'IP '.$ip.' добавлен в черный список');
        else session()->flash('error', 'Ошибка добавления IP в черный список');
    }



    /**
     * Удаление из черного списка
     *
     * @param string $ip
     * @return void
     */
    public function remove_black_list($ip){
        $res = null;

        $verify_black_list = $this->where('ip', $ip)->get();
        foreach ($verify_black_list as $str){
            $str->black_list_ip = 0;
            $str->comment = null;
            $res = $str->save();
        }

        if($res) session()->flash('status', 'IP '.$ip.' удален из черного списка');
        else session()->flash('error', 'Ошибка удаления IP из черного списка.');
    }



    /**
     * Удаление данных старше 90 дней
     *
     * @return void
     */
    public function remove_old(){

        $today = time();
        $time = $today - (86400*90);
        //Формат
        $old_time = date("Y-m-d H:i:s",$time);

        $old = $this->where('created_at', '<', $old_time)->get();
        foreach($old as $str){
            $str->delete();
        }
        session()->flash('status', 'Удалено '. count($old) . ' строк.');

    }

    /**
     * Проверка был ли такой IP в течении текущих суток (0-24)
     * Если да, то не добавляем в общий счетчик посетителей за день
     * @param string $ip
     * @param Carbon $date
     * @return Collection
     */
    public function find_ip_by_day($ip, $date){
        $time = $date->format('Y-m-d 00:00:00'); //0:00 полученного дня
        $time_now = $date->subSecond()->format('Y-m-d H:i:s'); //текущее время и день минус 1 секунда

        $res = $this->where('ip', $ip)
            ->whereBetween('created_at', [$time, $time_now])
            ->get();

        return $res;
    }


    /**
     * Преобразуем коллекцию к виду, где элементы с более поздней датой идут в начале
     * при этом часы/минуты/секунды в расчет не берутся
     * Используется для вывода в начале таблицы текущей даты и дальше по убыванию
     *
     * @param Collection $count_ip
     * @return Collection
     */
    public function reverse($count_ip){

        if(!$count_ip->isEmpty()){
            $array = [];
            $count = 0;
            $first_day = $count_ip->first()->created_at->format('Y-m-d');

            foreach ($count_ip as $item) {
                $one_day = $item->created_at->format('Y-m-d');

                if ($first_day != $one_day) {
                    $count++;
                    $first_day = $one_day;
                    $array[$count][] = $item;
                } else {
                    $array[$count][] = $item;
                }
            };

            return collect($array)->reverse()->collapse();
        }
        return $count_ip;
    }

}