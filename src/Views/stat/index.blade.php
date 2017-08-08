<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>
	{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>--}}
	{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">--}}

    <link href="{{asset('ksl-stat/css/style_ip.css')}}" rel="stylesheet">

</head>
    <body>
    <h3 class="stat_center">Статистика посещений по IP</h3>
    <div id="stat_ip">


        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif




        @include('Views::default', [
            'count_ip'=> $count_ip,
            'stat_ip' => $stat_ip,
        ])


		{!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

		{{ Form::hidden('reset', true)}}
		{!! Form::button('Сбросить фильтры',['class'=>'button-reset','type'=>'submit']) !!}

		{!! Form::close() !!}





		{{--<div class="container">--}}

			{{--<h1>Laravel Bootstrap Datepicker</h1>--}}

			{{--<input class="date form-control" type="text">--}}

		{{--</div>--}}






		<hr>



		<h3>Сформировать за указанную дату</h3>
		{!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

		{!! Form::text('date_ip', '',['class'=>'date_ip']) !!}
		{{--{{ Form::hidden('reset', true)}}--}}
		{!! Form::button('Отфильтровать',['class'=>'button-reset','type'=>'submit']) !!}

		{!! Form::close() !!}






	<hr>




		<h3>Сформировать за выбранный период </h3>
		{!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

		<div class="form-group">
		{{ Form::label('Начало', null, ['class' => 'control-label']) }}
		{!! Form::text('start_time', '',['class'=>'date_ip']) !!}
		</div>

        <div class="form-group">
		{{ Form::label('Конец', null, ['class' => 'control-label']) }}
		{!! Form::text('stop_time', '',['class'=>'date_ip']) !!}
        </div>

		{{--{{ Form::hidden('reset', true)}}--}}
		{!! Form::button('Отфильтровать',['class'=>'button-reset','type'=>'submit']) !!}

		{!! Form::close() !!}





		<hr>



        <h3>Сформировать по определенному IP</h3>
        {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

        <div class="form-group">
            {{ Form::label('IP', null, ['class' => 'control-label']) }}
            {!! Form::text('ip', '127.0.0.1') !!}
        </div>



        {{--{{ Form::hidden('reset', true)}}--}}
        {!! Form::button('Отфильтровать',['class'=>'button-reset','type'=>'submit']) !!}

        {!! Form::close() !!}







	<hr>



    <h3>Черный список IP</h3>
    <p>Под черным списком понимаются IP, по которым не нужна статистика, например IP администратора сайта.
       Поисковые боты отфильтровываются специальной функцией и попасть в общую статистику не должны.
    <br>По данным IP статистика не будет сохраняться с момента добавления в черный список.</p>

    <table>
        <tr class='tr_small'>
        <?php
//        $black_list = $count_model->count_black_list();
		//debug($black_list);
		echo "<h4>Сейчас в черном списке:</h4>";
        foreach($black_list as $key=>$value){
            echo "<td>". $value['ip'];
			if(!empty($value['comment'])) echo " - ". $value['comment'];
			echo "</td>";
        }
        IF (count($black_list)==0) echo "<td>Черный список пуст.</td>";
        ?>
        </tr>
    </table>
    <br>










    {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('IP', null, ['class' => 'control-label']) }}
        {!! Form::text('ip', '127.0.0.1') !!}
    </div>
    <div class="form-group">
        {{ Form::label('Комментарий', null, ['class' => 'control-label']) }}
        {!! Form::text('comment') !!}
    </div>
    {{ Form::hidden('add_black_list', true)}}
    {!! Form::button('Добавить в черный список',['type'=>'submit']) !!}
    {!! Form::close() !!}








	<br>



    {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::label('IP', null, ['class' => 'control-label']) }}
        {!! Form::text('ip', '127.0.0.1') !!}
    </div>

    {{ Form::hidden('del_black_list', true)}}
    {!! Form::button('Удалить из черного списка',['type'=>'submit']) !!}
    {!! Form::close() !!}



        <h3>Очистка базы данных <span class="font_min">(старше 90 дней)</span></h3>

        {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
        {{ Form::hidden('del_old', true)}}
        {!! Form::button('Удалить старые данные',['type'=>'submit']) !!}
        {!! Form::close() !!}









    <script type="text/javascript">

        $('.date_ip').datepicker({

            format: 'yyyy-mm-dd'

        });

    </script>

</body>
</html>