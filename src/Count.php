<?php

namespace Klisl\Statistics;

use Klisl\Statistics\Models\KslStatistic;


/**
 * Сохраняет в БД IP посетителя
 *
 * @package Klisl\Statistics
 */
class Count
{

    /**
     * Получение IP текущего посетителя, проверка на бота, поиск в черном списке
     * Сохранение в БД при прохождении фильтров
     *
     * @return void
     */
    public static function init(){

        $ip = \Request::ip();
        // if($ip == '127.0.0.1') return;

        $count_model = new KslStatistic();

        $str_url = \URL::full(); //URL текущей страницы c параметрами


		//Проверка на бота
		$bot_name = self::isBot2();

		if(!$bot_name){
			//Проверка в черном списке
			$black = $count_model->inspection_black_list($ip);

			if(!$black){
				$count_model->setCount($ip, $str_url, 0);
			}
		}
	}

    /**
     * Проверяет, является ли посетитель роботом поисковой системы.
     *
     * @param string $botname
     * @return bool|string
     */
    protected static function isBot1(&$botname = ''){
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

    //Альтернативный метод проверки на бота
    protected static function isBot2()
    {
        $is_bot = preg_match(
            "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i",
            $_SERVER['HTTP_USER_AGENT']
        );
        return $is_bot;
    }

}