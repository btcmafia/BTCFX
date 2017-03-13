<?php
namespace app\controllers;
use lithium\storage\Session;
use app\models\Users;
use app\models\Contractors;
use app\models\Timeslots;
//use MongoID;

class AjaxController extends \app\extensions\action\Controller {

        public function index() {

	return 'Ajax stuff here';
        }

	public function increaseavailability($slot) {

		$this->secure();
		$user_id = $this->get_user_id();


	        $timeslot = Timeslots::find('first', array(
                                 'conditions' => array(
                                                '_id' => $slot,
                                                'user_id' => $user_id)
                                         ));

	        if(count($timeslot) == 0) {

			$success = false;
			$ErrorMessage = 'Invalid timeslot';

			return $this->render(array('json' => compact('success', 'ErrorMessage')));
		} 

	//all good
	$CurrentAvailability = $timeslot['slots_available'];

	$success = true;
	$NewAvailability = $CurrentAvailability + 1;

	if($NewAvailability > $timeslot['default_max']) {

		$success = false;
		$ErrorMessage = 'Maximum jobs allowed already';
			
		return $this->render(array('json' => compact('success', 'ErrorMessage')));
	}

		$data = array(
			'slots_available' => $NewAvailability,
			'edited' => true,
			);

		//save
		$timeslot->save($data);

	return $this->render(array('json' => compact('success', 'CurrentAvailability', 'NewAvailability')));
	}



	public function decreaseavailability($slot) {


		$this->secure();
		$user_id = $this->get_user_id();


	        $timeslot = Timeslots::find('first', array(
                                 'conditions' => array(
                                                '_id' => $slot,
                                                'user_id' => $user_id)
                                         ));

	        if(count($timeslot) == 0) {

			$success = false;
			$ErrorMessage = 'Invalid timeslot';

			return $this->render(array('json' => compact('success', 'ErrorMessage')));
		} 

	//all good
	$CurrentAvailability = $timeslot['slots_available'];

	$success = true;
	$NewAvailability = $CurrentAvailability - 1;


	if(0 > $NewAvailability) {

		$success = false;
		$ErrorMessage = 'Jobs cannot be less than zero';
			
		return $this->render(array('json' => compact('success', 'ErrorMessage')));
	}

		$data = array(
			'slots_available' => $NewAvailability,
			'edited' => true,
			);

		//save
		$timeslot->save($data);

	return $this->render(array('json' => compact('success', 'CurrentAvailability', 'NewAvailability')));

	}



	public function decreasecontractoravailability($slot) {
/*
			$success = false;
			$ErrorMessage = 'Only office staff can do that!';

			return $this->render(array('json' => compact('success', 'ErrorMessage')));
			exit;
*/
		$user = Session::read('default');
		
		if($user['permissions']['office']==false) {

			$success = false;
			$ErrorMessage = 'Only office staff can do that!';

			return $this->render(array('json' => compact('success', 'ErrorMessage')));
			
		}


	        $timeslot = Timeslots::find('first', array(
                                 'conditions' => array(
                                                '_id' => $slot,
                                               // 'user_id' => $user_id
						)
                                         ));

	        if(count($timeslot) == 0) {

			$success = false;
			$ErrorMessage = 'Invalid timeslot';

			return $this->render(array('json' => compact('success', 'ErrorMessage')));
		} 

	//all good
	$CurrentAvailability = $timeslot['slots_available'];

	$success = true;
	$NewAvailability = $CurrentAvailability - 1;


	if(0 > $NewAvailability) {

		$success = false;
		$ErrorMessage = 'Jobs cannot be less than zero';
			
		return $this->render(array('json' => compact('success', 'ErrorMessage')));
	}

		$data = array(
			'slots_available' => $NewAvailability,
			'edited' => true,
			);

		//save
		$timeslot->save($data);

	return $this->render(array('json' => compact('success', 'CurrentAvailability', 'NewAvailability')));
 	}
	
	public function updatereadystatus() {

                $user = Session::read('default');

                if(''==$user) {

                        $success = false;
                        $ErrorMessage = 'Not signed in!';

                        return $this->render(array('json' => compact('success', 'ErrorMessage')));

                }

                $user_id = $user['_id'];


                $contractor = Contractors::find('first', array(
                                 	'conditions' => array(
                                                'user_id' => $user_id)
                                         ));

                if(count($contractor) == 0) {

                        $success = false;
                        $ErrorMessage = 'Invalid contractor';

                        return $this->render(array('json' => compact('success', 'ErrorMessage')));
                }
		
		if(false == $contractor['ready_to_go']) { 

			$new_ready_to_go = true;
			$ReadyMessage = 'I am ready to go now!';
		 
		} else {	

			$new_ready_to_go = false;
			$ReadyMessage = 'Mark me ready to go now!';
		     }

		$contractor->save(array('ready_to_go' => $new_ready_to_go));

		$success = true;

                        return $this->render(array('json' => compact('success', 'ReadyMessage')));
	} //function

}

?>
