<?php
if(isset($path[3]) AND !empty($path[3])){
	$action = $path[3];
}else{
	$action = "list";
}
?>
<?php if($action == "cat"): ?>

<?php else: ?>
	<table class="table table-bordered">
		<thead>
			<th>Nosaukums</th>
			<th>Apraksts</th>
			<th>Autors</th>
			<th>Pievienots</th>
			<th>Darbības</th>
		</thead>
		<tbody>
			<?php $videos = $db->fetchAll("SELECT * FROM `videos` ORDER BY `time` DESC", array()); ?>
			<?php foreach($videos as $video): ?>
				<tr>
					<td><?php echo $video['title']; ?></td>
					<td><?php echo $video['description']; ?></td>
					<td width="80px"><?php echo user::formatName($video['author_id'], true, true); ?></td>
					<td width="130px"><?php echo date("d/m/Y", $video['time']); ?> plkst. <?php echo date("H:i", $video['time']); ?></td>
					<td width="130px">
						<a href="<?php echo $c['url']; ?>/acp/videos/edit/<?php echo $video['id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
						<a href="<?php echo $c['url']; ?>/acp/videos/delete/<?php echo $video['id']; ?>"><button class="btn btn-danger btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity: 0.4;"></span> Dzēst</button></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>