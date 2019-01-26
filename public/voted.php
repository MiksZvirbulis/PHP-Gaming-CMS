<?php
if(isset($path[2]) AND !empty($path[2]) AND strlen($path[2]) == 32 AND user::isLoggedIn()){
	if($_SESSION['session_id'] == $path[2]){
		user::addMoney($_SESSION['user_id'], "0.05");
		$page = new page("Balsošana: paldies par Tavu balsojumu!", array("Balsošana", "Paldies par Tavu balsojumu!"));
		page::alert("Balsošana: paldies par Tavu balsojumu!", "danger");
		echo $_SERVER['HTTP_REFERER'];
	}else{
		$page = new poge("Balsošana: sesija nepastāv!", array("Balsošana", "Sesija nepastāv!"));
		page::alert("Balsošana: sesija nepastāv!", "danger");
	}
}else{
	$page = new page("Balsošana: sesija netika atrasta!", array("Balsošana", "Sesija netika atrasta!"));
	page::alert("Balsošana: sesija netika atrasta!", "danger");
}