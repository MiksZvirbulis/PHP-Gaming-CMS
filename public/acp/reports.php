<?php
if(isset($_POST['fixed'])){
	$report_id = $_POST['report_id'];
	foreach($_POST['fixed'] as $id => $key){
		$db->update("UPDATE `reports` SET `fixed` = 1 WHERE `id` = ?", array($report_id[$id]));
	}
	echo page::alert("Kļūdas reports veiksmīgi atzīmēts kā salabots!", "success");
}
?>
<table class="table table-bordered">
	<thead>
		<th>#</th>
		<th>Autors</th>
		<th>Datums</th>
		<th>Apraksts</th>
		<th>Kļūda labota?</th>
		<th>Darbības</th>
	</thead>
	<tbody>
		<?php $reports = $db->fetchAll("SELECT * FROM `reports` ORDER BY `fixed` ASC, `date` DESC"); ?>
		<form method="POST">
			<?php $i = 1; ?>
			<?php foreach($reports as $report): ?>
				<tr>
					<td><?php echo $i; ?><input type="hidden" name="report_id[<?php echo $i; ?>]" value="<?php echo $report['id']; ?>"></td>
					<td><?php echo user::formatName($report['author_id'], true, true); ?></td>
					<td><?php echo date("d/m/Y H:i", $report['date']); ?></td>
					<td><?php echo $report['description']; ?></td>
					<td><span class="glyphicon glyphicon-<?php echo ($report['fixed'] == 1) ? "ok" : "remove"; ?>"></span></td>
					<td>
						<?php if($report['fixed'] == 0): ?>
							<button type="submit" class="btn btn-primary" name="fixed[<?php echo $i; ?>]">Salabots</button>
						<?php endif; ?>
					</td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
		</form>
	</tbody>
</table>