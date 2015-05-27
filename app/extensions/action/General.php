<?php

function is_logged_in() {

	$user = Session::read('default');
	
		if ($user==""){
		
		return false;
		}

		else {

		return $user['_id'];
		}
}


?>
