<? require FUNCTIONS.'cronadds.php';?>
<div id="prog">
	<? if(empty($editid)):?>

		<table border="0" align="center">
			<tbody>
				<tr>
					<th>ID</th>
					<th>Minuto</th>
					<th>Hora</th>
					<th>Dia del mes</th>
					<th>Mes</th>
					<th>Dia de la semana</th>
					<th>Comando</th>
					<th>Accion</th>
				</tr>
				<pre>
				<? $w = $whilecron->whiledatos(); ?>
			</pre>
				<? foreach ($w as $i => $l):?>
					<? if($i == 'id') $count = count($l); ?>
						<? for ($k=0; $k < @$count; $k++):?>
							<tr>
								<td><?=$w['id'][$k]?></td>
								<td><?=$w['m'][$k]?></td>
								<td><?=$w['h'][$k]?></td>
								<td><?=$w['monthday'][$k]?></td>
								<td><?=$w['month'][$k]?></td>
								<td><?=$w['weekday'][$k]?></td>
								<td><?=$w['command'][$k]?></td>
								<td>
									<a href='/cron/<?=$w['id'][$k]?>'><img src='/img/edit.png' alt='Editar Tarea'></a>
									<form method='post' action='deletecron' style='display:inline'><input type='hidden' value='<?=$w['id'][$k]?>' name='id'><input type='image' src='/img/delete.png' /></form>
								</td>
							</tr>
						<? endfor?>
				<? endforeach?>
			</tbody>
		</table>
		<form method='post' action='addcron'><select name="croncommand" id="croncommand"><? foreach($cronadd as $i => $l):?><option value="<?=$i?>"><?=$i?></option><? endforeach?></select><input type='submit' value='+' name='add' /></form>
	<? else:?>
		<?=$editcron->recojerdatos("##".$editid)?>
		<h2>Recuerda que la ID tiene que comenzar con ##</h2>
		<table border="0">
			<tbody>
				<tr>
					<th>ID</th>
					<th>Minuto</th>
					<th>Hora</th>
					<th>Dia del mes</th>
					<th>Mes</th>
					<th>Dia de la semana</th>
					<th>Comando</th>
					<th>Accion</th>
				</tr>
				<tr>
					<form method="post" action="../editcron">
						<td><input type="text" name="id" id="id" value='<?=$editcron->id?>'></td>
						<td><input type="text" name='m' id='m' size="2" value="<?=$editcron->m?>"></td>
						<td><input type="text" name='h' id='h' size="2" value="<?=$editcron->h?>"></td>
						<td><input type="text" name='monthday' id='monthday' size="2" value="<?=$editcron->monthday?>"></td>
						<td><input type="text" name='month' id='month' size="2" value="<?=$editcron->month?>"></td>
						<td><input type="text" name='weekday' id='weekday' size="2" value="<?=$editcron->weekday?>"></td>
						<td><input type="text" name="command" id="command" size="40" value='<?=$editcron->command?>'></td>
						<td><input type="hidden" value="<?=$editcron->id?>" name="lastid" ><input type="submit" name="guardar" id="guardar" value="Guardar" /></td>
					</form>
				</tr>
			</tbody>
		</table>
	<? endif?>
</div>
