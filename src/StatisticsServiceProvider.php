<?php

namespace Klisl\Statistics;

use Illuminate\Support\ServiceProvider;
use Route;
use Request;


/**
 * @author Sergey <ksl80@ukr.net>
 * @package Klisl\Statistics
 */
class StatisticsServiceProvider extends ServiceProvider
{

    /**
     * Инициализация расширения
     *
     * @return void
     */
    public function boot()
    {

        Route::post('/statistics',['uses' =>'StatController@forms'])->name('forms');

        /*
         * Используется стандартное событие, срабатывающее после загрузки всех маршрутов
         * для получения названия текущего маршрута
         */
        Route::matched(function (){
            $name = \Route::currentRouteName();
            //получаем названия маршрутов по которым нужна статистика
            $routes = config('statistics.name_route');

            //Если по данному маршруту нужно собирать статистику и запрос типа GET
            if(in_array($name, $routes) && Request::isMethod('get')){
                Count::init();
            }
        });

        //Регистрация алиасов для стороннего пакета laravelcollective/html
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Html', 'Collective\Html\HtmlFacade');
        $loader->alias('Form', 'Collective\Html\FormFacade');

        //подключение файла маршрутов пакета
        include __DIR__.'/routes.php';

        $this->publishes([__DIR__ . '/../config/' => config_path()]);
		$this->publishes([__DIR__ . '/../database/' => database_path()]);
		$this->publishes([__DIR__ . '/../public/' => public_path()]);

        /*
         * Регистрируется (добавляем) каталог для хранения шаблонов
         */
        $this->loadViewsFrom(__DIR__ . '/Views/stat', 'Views');

    }


    /**
     * Регистрация сервис-провайдера стороннего пакета, указанного в зависимостях
     *
     * @return void
     */
    public function register()
    {
        \App::register('Collective\Html\HtmlServiceProvider');
    }

}
