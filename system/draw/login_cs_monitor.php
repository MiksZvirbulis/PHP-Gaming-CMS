<?php
define("IN_SYSTEM", true);
include_once("../config.php");
if(!empty($_GET['server']) && array_key_exists($_GET['server'], $servers)){
	$server = $servers[$_GET['server']];
}else{
	die();
}
try{
	$Query = new SourceQuery();
	$Query->Connect($server->ip, $server->port, 1, SourceQuery :: SOURCE);
	$info = $Query->GetInfo();
	?>
	<div class="monitor">
		<span class="align-left"><?=$info['HostName']?></span>
		<span class="align-center">(<?=$info['Players']?>/<?=$info['MaxPlayers']?>)</span>
		<span class="align-right"><a href="steam://connect/<?=$server->ip;?>:27015"><?=$server->ip;?></a></span>
	</div>
	<?php
}
catch(Exception $e){
	echo $e->getMessage( );
}
$Query->Disconnect();
?>