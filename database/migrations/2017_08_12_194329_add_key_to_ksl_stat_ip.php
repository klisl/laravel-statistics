<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Миграция
 * Создание таблицы kslStatistics
 */
class AddKeyToKslStatIp extends Migration
{

    public function up()
    {
        Schema::table('kslStatistics', function (Blueprint $table) {
            $table->index('ip', 'idx-kslStatistics-ip');
            $table->index('black_list_ip', 'idx-kslStatistics-black_list_ip');
            $table->index('created_at', 'idx-kslStatistics-created_at');

            $table->index(['created_at', 'black_list_ip'], 'idx-kslStatistics-created_at-black_list_ip');
            $table->index(['ip', 'created_at'], 'idx-kslStatistics-ip-created_at');
        });
    }

    public function down()
    {
        Schema::table('kslStatistics', function (Blueprint $table) {
            $table->dropIndex('idx-kslStatistics-ip');
            $table->dropIndex('idx-kslStatistics-black_list_ip');
            $table->dropIndex('idx-kslStatistics-created_at');
            $table->dropIndex('idx-kslStatistics-created_at-black_list_ip');
            $table->dropIndex('idx-kslStatistics-ip-created_at');
        });
    }



}
