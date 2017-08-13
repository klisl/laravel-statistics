<?php

namespace Klisl\Statistics;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Route;
use Request;


class StatisticsServiceProvider extends ServiceProvider
{

	
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


		//Публикуем конфигурационный файл
        $this->publishes([__DIR__ . '/../config/' => config_path()]);

		//Публикуем миграции
		$this->publishes([__DIR__ . '/../database/' => database_path()]);

		//Публикуем стили
		$this->publishes([__DIR__ . '/../public/' => public_path()]);


        /*
         * Регистрируется (добавляем) каталог для хранения шаблонов
         */
        $this->loadViewsFrom(__DIR__ . '/Views/stat', 'Views');


//		Schema::defaultStringLength(191);
    }

	
    public function register()
    {
        // Регистрация сервис-провайдера стороннего пакета, указанного в зависимостях
        \App::register('Collective\Html\HtmlServiceProvider');

    }

}
