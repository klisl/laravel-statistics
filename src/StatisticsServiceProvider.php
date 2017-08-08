<?php

namespace Klisl\Statistics;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Route;


class StatisticsServiceProvider extends ServiceProvider
{

	
    public function boot()
    {
        //Регистрация алиасов для стороннего пакета laravelcollective/html
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Html', 'Collective\Html\HtmlFacade');
        $loader->alias('Form', 'Collective\Html\FormFacade');


        include __DIR__.'/routes.php';

//		/*
//		 * Маршрут обрабатывающий POST запрос отправляемый формой с помощью AJAX
//		 */
//		Route::post('comment', ['uses' => 'App\Http\Controllers\CommentController@store', 'as' => 'comment']);
//
//
//		//Публикуем конфигурационный файл (config/comments.php)
//        $this->publishes([__DIR__ . '/../config/' => config_path()]);
//
//		//Публикуем CommentController и модель Comment
//		$this->publishes([__DIR__ . '/../app/' => app_path()]);
//
		//Публикуем миграции
		$this->publishes([__DIR__ . '/../database/' => database_path()]);

		//Публикуем стили
		$this->publishes([__DIR__ . '/../public/' => public_path()]);


        /*
         * Регистрируется (добавляем) каталог для хранения шаблонов
         */
        $this->loadViewsFrom(__DIR__ . '/views/stat', 'Views');


		Schema::defaultStringLength(191);
    }

	
    public function register()
    {
        // Регистрация сервис-провайдера стороннего пакета, указанного в зависимостях
        \App::register('Collective\Html\HtmlServiceProvider');



    }

}
