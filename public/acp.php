<?php
$page = new page("ACP", array("Administrācijas Kontroles Panelis"), false);

if(isset($path[2])){
	$path[2] = $path[2];
}else{
	$path[2] = "main";
}
?>
<style>
	input.form-control,
	textarea.form-control{
		width: 400px;
		height: 15px;
		margin: 15px auto;
	}

	select.form-control{
		width: 400px;
		margin: 15px auto;
	}
</style>
<div class="cblock">
	<?php
	if(file_exists($c['dir'] . "public/acp/" . $path[2] . ".php")){
		if($path[2] != "main"){
			include $c['dir'] . "public/acp/main.php";
		}
		include $c['dir'] . "public/acp/" . $path[2] . ".php";
	}else{
		include $c['dir'] . "public/acp/main.php";
	}
	?>
</div>