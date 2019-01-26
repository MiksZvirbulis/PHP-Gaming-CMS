<?php
if(isset($path[2]) AND !empty($path[2])){
	$action = $path[2];
}else{
	$action = "list";
}
?>
<?php if($action == "view" AND isset($path[3]) AND !empty($path[3])): ?>
	<?php
	$request = $path[3];
	$explode = explode("-", $request);
	$remove_id = array_slice($explode, 1);
	$title_seo = urldecode(implode("-", $remove_id));
	$tournament_id = $explode[0];
	$find_tournament = $db->count("SELECT `id` FROM `tournaments` WHERE `id` = ? AND `title_seo` = ?", array($tournament_id, $title_seo));
	if($find_tournament == 0){
		$page = new page("Pieprasītais turnīrs netika atrasts!", array("Pieprasītais turnīrs netika atrasts!"));
		echo page::alert("Pieprasītais turnīrs netika atrasts!", "danger");
	}else{
		$tournament = $db->fetch("SELECT * FROM `tournaments` WHERE `id` = ?", array($tournament_id));
		$page = new page($tournament['title'], array("Turnīru saraksts", $tournament['title']));
		if(isset($_POST['apply']) AND $tournament['status'] == "open" AND user::isLoggedIn()){
			$countPlayers = $db->count("SELECT `id` FROM `tournaments_players` WHERE `tournament_id` = ?", array($tournament['id']));
			if($countPlayers >= $tournament['total_players']){
				echo page::alert("Pieteikušies pietiekami daudz spēlētāju, lai turnīrs varētu turpināties.", "info");
			}else{
				$findPlayer = $db->count("SELECT `id` FROM `tournaments_players` WHERE `user_id` = ?", array($_SESSION['user_id']));
				if($findPlayer == 1){
					echo page::alert("Tu jau esi pieteicies šajā turnīrā!", "info");
				}else{
					$db->insert("INSERT INTO `tournaments_players` (`tournament_id`, `user_id`, `position`) VALUES (?, ?, 'quart')", array($tournament['id'], $_SESSION['user_id']));
					echo page::alert("Tu esi veiksmīgi pieteicies turnīrā. Gaidi ziņu no turnīra vadītāja!", "success");
				}
			}
		}
		?>
		<div class="panel panel-default">
			<div class="panel-heading" style="font-weight: bold;">
				<?php if($tournament['status'] == "open" AND user::isLoggedIn()): ?>
					<div class="pull-left" style="line-height: 30px;"><?php echo $tournament['title']; ?></div>
					<div class="form">
						<form method="POST">
							<button type="submit" class="green" name="apply">Pieteikties</button>
						</form>
					</div>
				<?php else: ?>
					<div class="pull-left"><?php echo $tournament['title']; ?></div>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
		</div>
		<table id="tourn_table" class="tourn_table_8 table">
			<?php
			if($tournament['status'] == "closed"){
				$quart_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'quart' AND `tournament_id` = ? ORDER BY `user_id` DESC", array($tournament['id']));
			}else{
				$quart_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'quart' AND `tournament_id` = ? ORDER BY RAND()", array($tournament['id']));
			}
			$semi_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'semi' AND `tournament_id` = ?", array($tournament['id']));
			$final_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'final' AND `tournament_id` = ?", array($tournament['id']));
			?>
			<tr>
				<td class="map"><img class="mapimage" src="<?php echo $c['url']; ?>/assets/images/maps/<?php echo $tournament['quart_map']; ?>.jpg"><div class="name"><?php echo $tournament['quart_map']; ?></div></td>
				<td class="map"><img class="mapimage" src="<?php echo $c['url']; ?>/assets/images/maps/<?php echo $tournament['semi_map']; ?>.jpg"><div class="name"><?php echo $tournament['semi_map']; ?></div></td>
				<td class="map"><img class="mapimage" src="<?php echo $c['url']; ?>/assets/images/maps/<?php echo $tournament['final_map']; ?>.jpg"><div class="name"><?php echo $tournament['final_map']; ?></div></td>
			</tr>
			<tr>
				<td class="final">Ceturtdaļfināls</td>
				<td class="final">Pusfināls</td>
				<td class="final">Fināls</td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[0])) ? "Brīva vieta" : user::formatName($quart_players[0]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="tm"><div class="unknown"><?php echo (empty($semi_players[0])) ? "Pusfināls" : user::formatName($semi_players[0]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[1])) ? "Brīva vieta" : user::formatName($quart_players[1]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td class="tm-b"></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td class="tm"><div class="unknown"><?php echo (empty($final_players[1])) ? "Fināls" : user::formatName($final_players[1]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[2])) ? "Brīva vieta" : user::formatName($quart_players[2]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td class="tm-b"></td>
				<td class="tm-b"></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="tm"><div class="unknown"><?php echo (empty($semi_players[1])) ? "Pusfināls" : user::formatName($semi_players[1]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td class="tm-b"></td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[3])) ? "Brīva vieta" : user::formatName($quart_players[3]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
				<td class="tm-b"></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td class="tm"><img class="winner" src="<?php echo $c['url']; ?>/assets/images/icons/gold_cup.png" alt=""/> <div class="unknown">Uzvarētājs</div></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[4])) ? "Brīva vieta" : user::formatName($quart_players[4]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
				<td class="tm-b"></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="tm"><div class="unknown"><?php echo (empty($semi_players[2])) ? "Pusfināls" : user::formatName($semi_players[2]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td class="tm-b"></td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[5])) ? "Brīva vieta" : user::formatName($quart_players[5]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td class="tm-b"></td>
				<td class="tm-b"></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td class="tm"><div class="unknown"><?php echo (empty($final_players[1])) ? "Fināls" : user::formatName($final_players[1]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[6])) ? "Brīva vieta" : user::formatName($quart_players[6]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td class="tm-b"></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="tm"><div class="unknown"><?php echo (empty($semi_players[3])) ? "Pusfināls" : user::formatName($semi_players[3]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td class="tm"><div class="unknown"><?php echo (empty($quart_players[7])) ? "Brīva vieta" : user::formatName($quart_players[7]['user_id'], true, true); ?></div><div class="score"></div></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="map margin"><img class="mapimage" src="<?php echo $c['url']; ?>/assets/images/maps/<?php echo $tournament['final_map']; ?>.jpg" alt=""/><div class="name"><?php echo $tournament['final_map']; ?></div></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="tm"><div class="unknown">Brīva vieta</div><div class="score"></div></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><div class="third">Cīņa par <span>3.</span> vietu</div></td>
				<td class="tm"><div class="unknown">Brīva vieta</div></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td class="tm"><div class="unknown">Brīva vieta</div><div class="score"></div></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<hr />
		<div class="well"><?php echo text::bbcode($tournament['information'], array("bbcode" => true, "emoticons" => true)); ?></div>
		<?php addons::returnComments("tournaments", $tournament['id']); ?>
		<?php
	}
	?>
<?php else: ?>
	<?php $page = new page("Turnīru saraksts", array("Turnīru saraksts")); ?>
	<table class="table table-bordered">
		<thead>
			<th>Nosaukums</th>
			<th>Spēlētāju kopskaits</th>
			<th>Sākuma datums</th>
			<th>Statuss</th>
		</thead>
		<tbody>
			<?php $tournaments = $db->fetchAll("SELECT * FROM `tournaments` ORDER BY `id` DESC"); ?>
			<?php foreach($tournaments as $tournament): ?>
				<tr>
					<td><a href="<?php echo $c['url']; ?>/tournaments/view/<?php echo $tournament['id'] . "-" . $tournament['title_seo']; ?>"><?php echo $tournament['title']; ?></a></td>
					<td>0 / <?php echo $tournament['total_players']; ?></td>
					<td><?php echo $tournament['start_date']; ?></td>
					<td><?php echo ($tournament['status'] == "open") ? "Pieteikšanās atvērta" : "Pieteikšanās slēgta"; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>