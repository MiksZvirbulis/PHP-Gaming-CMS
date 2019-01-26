<?php
$page = new page("Virtuālais Veikals", array("Virtuālais Veikals"));

if(isset($path[2])){
	$path[2] = $path[2];
}else{
	$path[2] = "home";
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
	if(file_exists($c['dir'] . "public/vshop/" . $path[2] . ".php")){
		if($path[2] != "home"){
			include $c['dir'] . "public/vshop/home.php";
			echo "<hr />";
		}
		include $c['dir'] . "public/vshop/" . $path[2] . ".php";
	}else{
		include $c['dir'] . "public/vshop/home.php";
	}
	?>
</div>