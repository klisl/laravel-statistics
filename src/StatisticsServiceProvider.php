<?php

namespace Klisl\Statistics;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Route;


class StatisticsServiceProvider extends ServiceProvider
{

	
    public function boot()
    {

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

		//Публикуем шаблоны
		$this->publishes([__DIR__ . '/../resources/' => resource_path()]);

//
		Schema::defaultStringLength(191);
    }

	
    public function register()
    {

	}

}
