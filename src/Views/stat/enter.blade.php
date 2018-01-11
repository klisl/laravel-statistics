<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('ksl-stat/css/style_ip.css')}}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

</head>
<body id="statistics-enter">

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif



        <div class="hentry group">

            <h3>Статистика посещений</h3>
            {!! Form::open(['url'=>route('enter_forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

            <div class="form-group">
                {{ Form::label('Ввод пароля', null, ['class' => 'control-label']) }}
                {!! Form::text('password') !!}
            </div>

            {{ Form::hidden('enter', true)}}
            {!! Form::button('Войти',['class'=>'button-reset','type'=>'submit']) !!}
            {!! Form::close() !!}


        </div>




</body>
</html>