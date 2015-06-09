<?php

namespace app\extensions\action;

use app\extensions\action\QueuedFunctions;

class CallQueue extends \lithium\action\Controller{

	public function __construct() {

	$i = 0;

	while($i <= 4) {

	//if it return false then it is locked, try again
                if(! $queue = new QueuedFunctions() ) {

                usleep(300000); // 0.3 sec
                }
                else {

			$q = $queue->get_queue();

			foreach($q as $q) {

				$queue->{$q['Type']}($q['Params']);
				$queue->delete_from_queue($q['_id']);
               		}
		break;
		 }

	$i++;

		}

	}
}
?>
