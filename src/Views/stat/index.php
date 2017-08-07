<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use yii\widgets\Pjax;
	
	common\modules\statistics\assets\StatAsset::register($this); //стили
?>  
    
    <h3 class="stat_center">Статистика посещений по IP</h3>
    <div id="stat_ip">

    <?php echo $this->render('default',[
		'count_ip'=> $count_ip,
		'stat_ip' => $stat_ip,
	]); ?>

	<?php $form = ActiveForm::begin(); ?>
		<?=$form->field($count_model, 'reset')->hiddenInput(['value' => true])->label(false)?>
		<div class="button-reset">
			<?=Html::submitButton('Сбросить фильтры'); ?>		
		</div>
	<?php ActiveForm::end(); ?>	
	<hr>	
		
	<?php $form = ActiveForm::begin(); ?>
        <h3>Сформировать за указанную дату</h3>					
		<?=$form->field($count_model, 'date_ip')->widget(DatePicker ::classname(), [
			'dateFormat' => 'dd.MM.yyyy',			
			'language' => 'ru',	
			'clientOptions' => [ 
				'yearRange' => '2015:2025',
				'changeMonth' => 'true',
				'changeYear' => 'true',
				'firstDay' => '1',
			]		  
		])->label(false) ?> 
		<?=Html::submitButton('Отфильтровать'); ?>		
	<?php ActiveForm::end(); ?>	
	<hr>	
    	
		<?php $form = ActiveForm::begin(); ?>

        <h3>Сформировать за выбранный период </h3>
		<?=$form->field($count_model, 'start_time')->widget(DatePicker ::classname(), [
			'dateFormat' => 'dd.MM.yyyy',
			'language' => 'ru',		  
		  'clientOptions' => [ 
				'yearRange' => '2015:2025',
				'changeMonth' => 'true',
				'changeYear' => 'true',
				'firstDay' => '1',  
			]		  
		])->label('Начало') ?> 
		<?=$form->field($count_model, 'stop_time')->widget(DatePicker ::classname(), [
			'dateFormat' => 'dd.MM.yyyy',
			'language' => 'ru',		  
		  'clientOptions' => [ 
				'yearRange' => '2015:2025',
				'changeMonth' => 'true',
				'changeYear' => 'true',
				'firstDay' => '1',  
			]		  
		])->label('Конец &nbsp;')  ?> 
		<?=Html::submitButton('Отфильтровать'); ?>
		
	<?php ActiveForm::end(); ?>		
	<hr>	
		
      
	<?php $form = ActiveForm::begin(); ?>

        <h3>Сформировать по определенному IP</h3>
		<?=$form->field($count_model, 'ip', [
		'inputOptions' => [
			'size'=> 20,
		]])->textInput(['value'=>'127.0.0.1'])->label('IP') ?> 
		<?=Html::submitButton('Отфильтровать'); ?>
		
	<?php ActiveForm::end(); ?>	
	<hr>
		

		
    <h3>Черный список IP</h3>
    <p>Под черным списком понимаются IP, по которым не нужна статистика, например IP администратора сайта.
       Поисковые боты отфильтровываются специальной функцией и попасть в общую статистику не должны.
    <br>По данным IP статистика не будет сохраняться с момента добавления в черный список.</p>
	
    <table>
        <tr class='tr_small'>
        <?php 
        $black_list = $count_model->count_black_list();  
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
	
	<?php $form = ActiveForm::begin(); ?>	

            <?=$form->field($count_model, 'ip', [
		'inputOptions' => [
			'size'=> 20,
		]])->textInput(['value'=>'127.0.0.1'])->label('IP') ?> 

            <?=$form->field($count_model, 'comment', [
		'inputOptions' => [
			'size'=> 20,
		]])->label('Комментарий') ?> 

	

	<?=$form->field($count_model, 'add_black_list')->hiddenInput(['value' => true])->label(false)?>
	<?=Html::submitButton('Добавить в черный список'); ?>		
	<?php ActiveForm::end(); ?>	
	
	<br>
	<?php $form = ActiveForm::begin(); ?>		
	<?=$form->field($count_model, 'ip', [
		'inputOptions' => [
			'size'=> 20,
		]])->textInput(['value'=>'127.0.0.1'])->label('IP') ?> 
	<?=$form->field($count_model, 'del_black_list')->hiddenInput(['value' => true])->label(false)?>
	<?=Html::submitButton('Удалить из черного списка'); ?>
		
	<?php ActiveForm::end(); ?>		
	<hr>
	
 <h3>Статистика по поисковым роботам за последний месяц</h3> 
	<?php Pjax::begin(['enablePushState' => false]); ?>
 	<?php $form = ActiveForm::begin([
		'options' => [
			'data-pjax' => true,
		]]); ?>			
	<?=$form->field($bot_model, 'get_bot_stat')->hiddenInput(['value' => true])->label(false)?>
	<?=Html::submitButton('Сформировать'); ?>	
	<?php ActiveForm::end(); ?>	
	<?php Pjax::end(); ?>
	<hr>
 
	<h3>Очистка базы данных <span class="font_min">(старше 90 дней)</span></h3> 
	<?php Pjax::begin(['enablePushState' => false]); ?>
 	<?php $form = ActiveForm::begin([
		'options' => [
			'data-pjax' => true,
		]]); ?>		
	<?=$form->field($count_model, 'del_old')->hiddenInput(['value' => true])->label(false)?>
	<?=Html::submitButton('Удалить старые данные'); ?>	
	<?php ActiveForm::end(); ?>	
	<?php Pjax::end(); ?>
	</div>