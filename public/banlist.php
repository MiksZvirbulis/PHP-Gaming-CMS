<?php
$page = new page("Banu Saraksts", array("Banu Saraksts"), false);
if(isset($_POST['search']) AND isset($_POST['query']) AND !empty($_POST['query'])){
	header("Location: " . $c['url'] . "/banlist/search/" . $_POST['query']);
}
if(isset($path[2]) AND $path[2] == "search" AND isset($path[3]) AND !empty($path[3])){
	$query = $path[3];
	$query = "%$query%";
	$bans_count = $db->count("SELECT `bid` FROM `amx_bans` WHERE `player_ip` LIKE ? OR `player_nick` LIKE ? OR `admin_nick` LIKE ? OR `ban_reason` LIKE ?", array($query, $query, $query, $query));
	list($pager_template, $limit) = page::pagination(10, $bans_count, $c['url'] . "/banlist/search/" . $path[3] . "/page/", 5);
	$bans = $db->fetchAll("SELECT `bid`, `player_nick`, `admin_nick`, `ban_reason` FROM `amx_bans` WHERE `player_ip` LIKE ? OR `player_nick` LIKE ? OR `admin_nick` LIKE ? OR `ban_reason` LIKE ? ORDER BY `ban_created` DESC $limit", array($query, $query, $query, $query));
}else{
	$bans_count = $db->count("SELECT `bid` FROM `amx_bans`");
	list($pager_template, $limit) = page::pagination(10, $bans_count, $c['url'] . "/banlist/page/", 3);
	$bans = $db->fetchAll("SELECT `bid`, `player_nick`, `admin_nick`, `ban_reason` FROM `amx_bans` ORDER BY `ban_created` DESC $limit");
}
if(isset($path[2]) AND !empty($path[2]) AND is_numeric($path[2])){
	$find_ban = $db->count("SELECT `bid` FROM `amx_bans` WHERE `bid` = ?", array($path[2]));
	if($find_ban > 0){
		echo '<script type="text/javascript">window.onload = function(){ loadBan(' . $path[2] . '); };</script>';
	}
}
?>
<table class="table table-bordered pull-left" style="width: 65%;">
	<thead>
		<th width="220px">Spēlētājs</th>
		<th width="220px">Administrators</th>
		<th width="220px">Iemesls</th>
	</thead>
	<tbody>
		<?php foreach($bans as $ban): ?>
			<tr class="pointer" onClick="loadBan(<?php echo $ban['bid']; ?>)">
				<td><?php echo text::limit($ban['player_nick'], 20); ?></td>
				<td><?php echo text::limit($ban['admin_nick'], 20); ?></td>
				<td><?php echo text::limit($ban['ban_reason'], 20); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<div class="pull-right" style="width: 33%; margin: 0 5px 0 5px;">
	<form method="POST">
		<div style="width: 260px; margin: 0 auto;">
			<input autocomplete="off" type="text" name="query" placeholder="Meklēt..." class="pull-left" style="margin: 5px 0 5px 0;" value="<?php echo (isset($path[2]) AND $path[2] == "search" AND isset($path[3])) ? $path[3] : ""; ?>">
			<button id="search_button" type="submit" name="search" class="pull-left" style="margin: 5px 0 5px 0;"><span class="glyphicon glyphicon-search"></span></button>
			<div class="clear"></div>
		</div>
	</form>
	<div id="ban">
		<?php echo page::alert("Izvēlies banu!", "info"); ?>
	</div>
	<table class="table table-bordered" style="margin-top: 5px;">
		<thead>
			<th>Vieta</th>
			<th>Administrators</th>
			<th>Banu Skaits</th>
		</thead>
		<tbody>
			<?php $locate = $db->fetchAll("SELECT COUNT(*) AS `total`, `admin_nick` FROM `amx_bans` GROUP BY `admin_nick` ORDER BY COUNT(*) DESC LIMIT 3"); ?>
			<?php $i = 1; ?>
			<?php foreach($locate as $result): ?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $result['admin_nick']; ?></td>
					<td><?php echo $result['total']; ?></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div class="clear"></div>
<?php echo (empty($bans)) ? "" : $pager_template; ?>