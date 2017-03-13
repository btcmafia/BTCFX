<?php
namespace app\controllers;

use lithium\storage\Session;
use app\models\ActiveData;
use app\models\Jobs;
use app\models\Bookings;
use app\models\Timeslots;

class CustomersController extends \app\extensions\action\Controller {

        public function index() {

        $cookie_id = Session::read('default');

	$active_data = ActiveData::find('first', array(
				'conditions' => array(
					'_id' => $cookie_id
					)
				));

	if('' == $active_data['user_id']) return $this->redirect('/login/');

		$user = $active_data;

        return( compact('user') );
        }


	public function viewbooking($job_id, $visit_id, $customer_id, $action = false) {

		$booking = Bookings::find('first', array(
				'conditions' => array(
					'_id' => $visit_id,
					'job_id' => $job_id,
					'customer_id' => $customer_id,
					)
				));

				if(0 == count($booking)) {
	
				$error = 'Invalid Job, Visit or Customer ID';
		
				return compact('error');
				}
	
	
			if($booking['status']['code'] != 0) {

			$error = 'You have already confirmed this appointment. We are waiting for the contractor to confirm acceptance and will email you when it is confirmed.';

			return compact('error');
			}

		$data['status']['text'] = 'pending contractor acceptance';
		$data['status']['code'] = 1;
		
		$timeslot = Timeslots::find('first', array(
					'conditions' => array(
						'_id' => $booking['timeslot_id'],
						'user_id' => (string) $booking['contractor_id'],
							)
						));
	
				if(0 == count($timeslot)) die('Invalid timeslot');


				//add the job to the contractor's timeslot
				$jobs_booked = array(
					'job_id' => (string) $job_id,
					'visit_id' => (string) $visit_id,
					'service_required' => $booking['service_required'],
					'visit_description' => $booking['visit_title'],
					'service_address' => $booking['service_address'],
					'visit_status' => 'unconfirmed', 
					);		

				$timeslot_data = array(
						"bookings.$visit_id" => $jobs_booked
						);
	
			$booking->save($data);
			$timeslot->save($timeslot_data);
		
			$message = 'Appointment request confirmed';

		return compact('message', 'booking');	

	}
}



?>
