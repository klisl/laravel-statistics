<?php
	//debug($count_ip);
	$transition = 1; //счетчик переходов на страницы
	$count = 0; //счетчик посетителей
	$count_day = 0;
	$num_ip = ''; 
	//получаем первую дату из массива объектов
	if (isset($count_ip[0])){
		$date = $count_ip[0]->created_at->format('d.m.Y');
	} else $date = null;

//	dd($count_ip[0]->created_at->format('d.m.Y'));
	?>
	 
	<table class='get_table'>
			<thead>
			 <tr>
				<th>Переходы на страницы сайта</th>
				<th>IP</th>
				<th>URL просматриваемой страницы</th>
				<th>Время посещения</th>
			</tr>
			</thead>
			<tbody> 
	<?php foreach ($count_ip as $key=>$value){

			//кол-во посетителей по дням (вывод последнего дня после цикла)
			if($date && $date != $value->created_at->format('d.m.Y')) {
				echo $date . ' - '. $count_day . '<br>';

				$date = $value->created_at->format('d.m.Y');
//                dd($value->created_at->format('d.m.Y'));
				$count_day = 0;
			}
			if ($stat_ip) $count_day++; //для фильтра по определенному IP
			
			//Если сменился IP, то включаем счетчики
			if ($num_ip != $value->ip){
				$num_ip = $value->ip;
				$transition = 1;
				$count++;
				if (!$stat_ip) $count_day++; //для фильтра по определенному IP
			} else {
				$transition++;
			} 

			echo "<tr ";
			if ($transition == 1) {
			   echo "class='tr_first'><td colspan='4'>Новый посетитель.</td></tr>"; 				
		    } else {
			   echo ">"; 
		    }
			echo "<td>$transition</td>
				<td><a href='http://speed-tester.info/ip_location.php?ip=".$value->ip."'>".$value->ip."</a></td>  	
				<td><a href='".$value->str_url."'>".$value->str_url."</a></td>                     
				<td>".$value->created_at->format('d.m.Y')."</td></tr>";
			  
	}
				//вывод кол-ва посетителей за последнее число 
				if($date) echo $date . ' - '. $count_day . '<br>';
	?>
			<p>Всего посетителей за период - <?=$count?></p>		
		</tbody>
		</table>