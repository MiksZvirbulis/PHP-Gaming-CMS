<?php
if(isset($path[2]) AND !empty($path[2])){
	$action = $path[2];
}else{
	$action = "amx";
}
?>
<?php if($action == "admins" AND user::hasFlag("other")): ?>
	<?php $page = new page("Adminu saraksts", array("Adminu saraksts")); ?>
	<div id="usercp" style="margin-bottom: 10px;">
		<ul>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/amx/admins">Adminu saraksts</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/amx/ban">Pievienot banu</a></li>
		</ul>
	</div>
	<table class="table table-bordered" style="border-collapse: collapse;">
		<thead>
			<tr>
				<th>Spēlētāja vārds</th>
				<th>Pieejas flagi</th>
			</tr>
		</thead>
		<tbody>
			<?php $admins = $db->fetchAll("SELECT * FROM `amx_amxadmins` WHERE `access` != 'abcdefghijklmnopqrstu'"); ?>
			<?php foreach($admins as $admin): ?>
				<tr data-toggle="collapse" data-target="#admin_<?php echo $admin['id']; ?>" class="accordion-toggle pointer">
					<td><?php echo $admin['username']; ?></td>
					<td><?php echo $admin['access']; ?></td>
				</tr>
				<tr>
					<td colspan="2" class="hiddenRow">
						<div class="accordian-body collapse" id="admin_<?php echo $admin['id']; ?>">
							<div class="form">
								<form method="POST" style="margin: 10px;" id="editAdmin" class="editAdmin_<?php echo $admin['id']; ?>">
									<div id="alert_<?php echo $admin['id']; ?>"></div>
									<input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
									<input type="text" class="control" name="flag" placeholder="Pieejas flags, kuru noņemt" maxlength="1">
									<input type="text" class="control" name="reason" placeholder="Iemesls">
									<input type="text" class="control" name="link" placeholder="Rediģēšanas iemesla pierādījums ( saite uz tēmu vai demo )">
									<button type="submit" class="green" style="margin-bottom: 5px;" data-id="<?php echo $admin['id']; ?>">Pieprasīt</button>
								</form>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<hr />
	<div class="info_bar" style="margin: 10px 0 10px 0;">
		Pieejas flagu skaidrojumi
	</div>
	<table class="table table-bordered">
		<thead>
			<th>Pieejas flags</th>
			<th>Nozīme</th>
		</thead>
		<tbody>
			<tr>
				<td>a</td>
				<td>imunitāte - kick/ban/slay/slap komandas nestrādā uz šo spēlētāju</td>
			</tr>
			<tr>
				<td>b</td>
				<td>slota rezervācija</td>
			</tr>
			<tr>
				<td>c</td>
				<td>amx_kick</td>
			</tr>
			<tr>
				<td>d</td>
				<td>amx_ban un amx_unban</td>
			</tr>
			<tr>
				<td>e</td>
				<td>amx_slay un amx_slap</td>
			</tr>
			<tr>
				<td>f</td>
				<td>amx_map</td>
			</tr>
			<tr>
				<td>g</td>
				<td>amx_cvar</td>
			</tr>
			<tr>
				<td>h</td>
				<td>amx_cfg</td>
			</tr>
			<tr>
				<td>i</td>
				<td>amx_chat</td>
			</tr>
			<tr>
				<td>j</td>
				<td>amx_vote un citas vote komandas</td>
			</tr>
			<tr>
				<td>k</td>
				<td>sv_password - caur amx_cvar</td>
			</tr>
			<tr>
				<td>l</td>
				<td>amx_rcon un rcon_password - caur amx_cvar</td>
			</tr>
			<tr>
				<td>m</td>
				<td>pielāgots līmenis A - VIP un citi spraudņi</td>
			</tr>
			<tr>
				<td>n</td>
				<td>pielāgots līmenis B (cits spraudnis)</td>
			</tr>
			<tr>
				<td>o</td>
				<td>pielāgots līmenis C (cits spraudnis)</td>
			</tr>
			<tr>
				<td>p</td>
				<td>pielāgots līmenis D (cits spraudnis)</td>
			</tr>
			<tr>
				<td>r</td>
				<td>pielāgots līmenis E (cits spraudnis)</td>
			</tr>
			<tr>
				<td>s</td>
				<td>pielāgots līmenis F (cits spraudnis)</td>
			</tr>
			<tr>
				<td>t</td>
				<td>pielāgots līmenis H - VIP</td>
			</tr>
			<tr>
				<td>u</td>
				<td>izvēļņu pieeja - _menu, amxmodmenu</td>
			</tr>
			<tr>
				<td>z</td>
				<td>parasts spēlētājs - spēlētājs, kas nav admins</td>
			</tr>
		</tbody>
	</table>
<?php elseif($action == "ban" AND user::hasFlag("other")): ?>
	<?php $page = new page("Pievienot banu", array("Pievienot banu")); ?>
	<div id="usercp" style="margin-bottom: 10px;">
		<ul>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/amx/admins">Adminu saraksts</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/amx/ban">Pievienot banu</a></li>
		</ul>
	</div>
	<div class="form">
		<?php
		if(isset($_POST['addBan'])){
			$errors = array();

			if(empty($_POST['player_ip'])){
				$errors[] = "Tu aizmirsi ievadīt spēlētāja IP adresi!";
			}else{
				if(filter_var($_POST['player_ip'], FILTER_VALIDATE_IP) === false){
					$errors[] = "Norādītā IP adrese nav valīda!";
				}else{
					$find_ban = $db->count("SELECT `bid` FROM `amx_bans` WHERE `player_ip` = ?", array($_POST['player_ip']));
					if($find_ban > 0){
						$errors[] = "Bans ar ievadīto IP adresi jau pastāv!";
					}
				}
			}

			if(empty($_POST['player_nick'])){
				$errors[] = "Tu aizmirsi ievadīt spēlētāja vārdu!";
			}

			if(empty($_POST['ban_reason'])){
				$errors[] = "Tu aizmirsi ievadīt bana iemeslu!";
			}

			if($_POST['ban_length'] == ""){
				$errors[] = "Tu aizmirsi ievadīt bana ilgumu!";
			}else{
				if(!is_numeric($_POST['ban_length'])){
					$errors[] = "Bana ilgumam jābūt norādītam cipara vērtībā!";
				}
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					echo page::alert($error, "danger");
				}
			}else{
				$db->insert("INSERT INTO `amx_bans` (`player_ip`, `player_id`, `player_nick`, `admin_nick`, `ban_type`, `ban_reason`, `ban_created`, `ban_length`, `server_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
					$_POST['player_ip'],
					$_POST['player_id'],
					$_POST['player_nick'],
					user::formatName($_SESSION['user_id'], false, false, false),
					"SI",
					$_POST['ban_reason'],
					time(),
					$_POST['ban_length'],
					"website"
					));
				echo page::alert("Bans veiksmīgi pievienots!", "success");
			}
		}
		?>
		<form method="POST">
			<div id="alerts"></div>
			<input type="text" class="control" name="player_ip" placeholder="IP adrese">
			<input type="text" class="control" name="player_id" placeholder="SteamID">
			<input type="text" class="control" name="player_nick" placeholder="Spēlētāja vārds">
			<input type="text" class="control" name="ban_reason" placeholder="Bana iemesls">
			<input type="text" class="control" name="ban_length" placeholder="Bana ilgums minūtēs" value="0">
			<small>Norādi <strong>0</strong> priekš mūžīga bana.</small>
			<button type="submit" name="addBan" class="green">Pievienot</button>
		</form>
	</div>
<?php elseif($action == "punished"): ?>
	<?php $page = new page("Sodīto adminu saraksts", array("Sodīto adminu saraksts")); ?>
	<?php echo page::alert("Šeit Tu atradīsi sodīto adminu sarakstu. Admins, noņemtais flags un iemesls ar pierādījumiem!", "info"); ?>
	<table class="table table-bordered">
		<thead>
			<th>Admins</th>
			<th>Noņemtais flags</th>
			<th>Iemesls</th>
			<th>Pierādījumi</th>
			<th>BanTeam</th>
		</thead>
		<tbody>
			<?php $logs = $db->fetchAll("SELECT * FROM `admin_edit_log` WHERE `approved` = 1 ORDER BY `time` DESC"); ?>
			<?php foreach($logs as $log): ?>
				<tr>
					<td><?php echo $log['admin_nickname']; ?></td>
					<td><strong><?php echo $log['flag']; ?></strong></td>
					<td><?php echo $log['reason']; ?></td>
					<td><?php echo text::bbcode($log['link'], array("bbcode" => true, "media" => false)); ?></td>
					<td><?php echo user::formatName($log['author_id']); ?><input type="hidden" name="log_id[<?php echo $log['id']; ?>]" value="<?php echo $log['id']; ?>"></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<?php $page = new page("AMX Darbības", array("AMX Darbības")); ?>
	<ul class="nav nav-tabs margin">
		<li class="active"><a href="#checkAdmin" data-toggle="tab">Pārbaudīt ACC</a></li>
		<li><a href="#extra" data-toggle="tab">Papildināt ACC</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="checkAdmin">
			<div class="form">
				<form method="POST" id="checkAdmin">
					<input type="text" class="control" name="username" placeholder="Spēlētāja vārds">
					<input type="password" class="control" name="password" placeholder="Spēlētāja parole">
					<button type="submit" class="green">Pārbaudīt</button>
				</form>
				<div class="clear"></div>
				<hr />
				<div id="results"></div>
			</div>
		</div>

		<div class="tab-pane fade" id="extra">
			<?php echo page::alert("Drīzumā!", "success"); ?>
		</div>
	</div>
<?php endif; ?>