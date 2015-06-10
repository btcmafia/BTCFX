<?php

namespace app\extensions\action;

use app\extensions\action\QueuedFunctions;

class CallQueue extends \lithium\action\Controller{

	public function __construct() {

	$i = 0;
	while($i <= 4) {

	//if it return false then it is locked, try again
                if(! $queue = new QueuedFunctions() ) {
                
		usleep(100000); // 0.1 sec
                }
                else {

			$q = $queue->get_queue();
			
			foreach($q as $q) {
//echo "Type: {$q['Type']}<br />";
//print_r($q['Params']);
//die;				
				$queue->{$q['Type']}($q['Params']);
				$queue->delete_from_queue($q['_id']);
               		}

		$queue->release_lock();
		break;
		 }

	$i++;

		}

	}
}
?>
