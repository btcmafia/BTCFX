<?php
namespace app\controllers;

use app\models\Timeslots;
use app\models\Contractors;
use app\models\Bookings;
use app\models\Services;
use app\extensions\action\CallQueue;
use lithium\storage\Session;
use lithium\data\Connections;
use MongoDate;


class ContractorsController extends \app\extensions\action\Controller {

        public function index() {

	    $user = Session::read('default');
        if ($user==""){ return $this->redirect('/login'); }


	return 'Contractors Schedule';
        }


	public function schedule($page = 0) {

        $this->secure('contractor');

        $user_id = $this->get_user_id();

        $title = 'My Availability';


	//So we can check they have some active services and we'll need the ready status later
	$contractor = Contractors::find('first', array(
				'conditions' => array(
					'user_id' => $user_id)
				));

		if($contractor['services_active'] != true) {

			$services_active = 'no';

			return compact('title', 'services_active');
			exit;
		}


	$day = time() + (60*60*24 * $page);
	$day_name = date('l', $day);

	$search_day = date('z', $day) + 1; //date function starts from zero not 1
	$search_year = date('Y', $day);

	      //get slots for the requested day
              $timeslots = Timeslots::find('all', array(
                                     'conditions' => array(
                                       		'user_id' => $user_id,
                                        	'period.day' => $search_day,
						'period.year' => $search_year,
					),
					'order' => array('period.order' => 'ASC'),
					 ));

		if(count($timeslots) == 0) { die('<h1>Schedule not found.</h1> <p><a href="/cronjob/uj83hrwuisdui86236/3/">Clicking here may help</a>.</p><p>Alternatively, contact admin.</p>'); }


	//some counters for a messed up ordering system
	$small_num = 0;
	$big_num = 100;

		//should return 4 timeslots for the day
		foreach($timeslots as $timeslot) {

$jobs_booked = array();
foreach( $timeslot['bookings'] as $booking ) {

	//until contractor has accepted the job we remove house / flat [all] numbers from the address
	if($booking['visit_status'] == 'unconfirmed') {
	$booking['service_address']['address_1'] = preg_replace('/[0-9]+/', '', $booking['service_address']['address_1']);
	$booking['service_address']['address_2'] = preg_replace('/[0-9]+/', '', $booking['service_address']['address_2']);
	}

$booking['service_address'] = $this->format_address($booking['service_address']);

$jobs_booked[] = $booking;

}

		$periods[] = array('timeslot_id' => $timeslot['_id'], 'slots_available' => $timeslot['slots_available'], 'period_nicename' => $timeslot['period_nicename'], 'period_details' => $this->period_details($timeslot['period_nicename']), 'jobs_booked' => $jobs_booked, 'order' => $timeslot['period']['order']);

		

			//jobs booked?
			foreach($timeslot['jobs_booked'] as $job) {

				if($job['visit_status'] == 'unconfirmed') {

				$small_num++;
				$jobs[$small_num] = $job;
				}

				else {

					$big_num++;
					$jobs[$big_num] = $job;
					}
				}
		}

			//reorder jobs booked so unconfirmed jobs are first then in date order
			//$jobs = 

	
	    if(false == $contractor['ready_to_go']) {

                        $ready_status = 'Mark me ready to go now!';

                } else {

                        $ready_status = 'I am ready to go now!';
                     }


// Pass the data to the View.
    $this->set(compact('periods', 'day_name', 'page'));
    $this->set(compact('ready_status'));


	return; // $this->render(['layout' => 'mobile']);

	}


	public function services() {

	$title = 'Services Offered';

        $this->secure('contractor');

        $user_id = $this->get_user_id();

		$contractor = Contractors::find('first', array(
                                'conditions' => array(
                                        'user_id' => $user_id)
                                ));


         if($this->request->data) {

	$data['services_active'] = false;

	// 	$data = array();

                        foreach($contractor['services'] as $service_name => $foo) {

                        $service_id = (string) $foo['service_id'];

                        	
				if(isset($this->request->data[$service_id])) {

                        	$data['services'][$service_name] = array('service_id' => $service_id, 'active' => true, 'allowed' => $foo['allowed'], 'trade' => $foo['trade'], 'service_category' => $foo['service_category']);
				$data['services_active'] = true;
                        	}
                      		else {

                        		$data['services'][$service_name] = array('service_id' => $service_id, 'active' => false, 'allowed' => $foo['allowed'], 'trade' => $foo['trade'], 'service_category' => $foo['service_category']);
                        	}

                        } //foreach 
                       
			$contractor->save($data);

                        $message = 'Updated';

        } //end form submitted

	
		foreach($contractor['services'] as $service_name => $data) {

		if(! $data['allowed']) continue;

		$services[$data['trade']][$data['service_category']][] = array('service_name' => $service_name, 'service_id' => $data['service_id'], 'allowed' => $data['allowed'], 'active' => $data['active']);
	
		}

	return compact('title', 'services', 'message');	
	}


	public function viewbookings($status = 'all') {

	$this->secure('contractor');

	$user_id = $this->get_user_id();

		$visits = Bookings::find('all', array(
				'conditions' => array(
					'status' => $status,	
					)
				));
	}


	public function viewjob($visit_id) {

	$this->secure('contractor');

	$user_id = $this->get_user_id();

		/*
		$timeslot = Timeslots::find('first', array(
					'conditions' => array(
						'user_id' => $user_id,
						"bookings.$visit_id.visit_id" => $visit_id,
						)
					));
		*/

		$job = Bookings::find('first', array(
					'conditions' => array(
						'_id' => $visit_id,
						'contractor_id' => $user_id,
						)
					));

		if(0 == count($job)) die('Invalid Visit ID');

		//remove house numbers from address and phone and email if contractor has not accepted
		if($job['status']['code'] <= 1) { 

			$job['service_address']['address_1'] = preg_replace('/[0-9]+/', '', $job['service_address']['address_1']);
			$job['service_address']['address_2'] = preg_replace('/[0-9]+/', '', $job['service_address']['address_2']);
			$job['service_address']['phone'] = '';
			$job['service_address']['email'] = '';
		}

		$job['service_address']['address'] = $this->format_address($job['service_address']);


	return compact('title', 'job');
	}


	public function jobreport($visit_id) {

	$title = 'Submit Job Report';
	$this->secure('contractor');
	$user_id = $this->get_user_id();


		if($this->request->data) {

			if(! $this->request->data['use_timesheet']) {

			
			}


		}

		$booking = Bookings::find('first', array(
					'conditions' => array(
						'_id' => $visit_id,
						'contractor_id' => $user_id,
						)
					));

		if(0 == count($booking)) die('Invalid Visit ID');

		//format the timesheet

		$i = 0;
		foreach($booking['timesheet'] as $a) {

			$timesheet[$i]['start'] = date("D H:i", $a['start']->sec);
			$timesheet[$i]['finish'] = date("D H:i", $a['finish']->sec);
			$timesheet[$i]['minutes'] = floor($a['minutes']);
		$i++;
		} 

	return compact('title', 'booking', 'timesheet');
	}

	public function acceptbooking($visit_id) {

	$this->secure('contractor');
	$user_id = $this->get_user_id();

		$booking = Bookings::find('first', array(
					'conditions' => array(
						'_id' => $visit_id,
						'contractor_id' => $user_id,
						)
					));

		if(0 == count($booking)) die('Invalid Visit ID');
		
		$data['status']['text'] = 'confirmed';
		$data['status']['code'] = 2;

			//update the status in timeslots
			$timeslot = Timeslots::find('first', array(
					'conditions' => array(
						'_id' => $booking['timeslot_id'],
						'user_id' => $user_id
						)
					));
			//save
			if(0 != count($timeslot)) $timeslot->save(array("bookings.$visit_id.visit_status" => 'confirmed'));


		if($booking->save($data)) $this->redirect("/contractors/viewjob/$visit_id/");

		else die("Error accepting job! Please contact admin ASAP with Booking Ref: $visit_id");
	}


	public function onmyway($visit_id) {

	$this->secure('contractor');

	$user_id = $this->get_user_id();

		$booking = Bookings::find('first', array(
					'conditions' => array(
						'_id' => $visit_id,
						'contractor_id' => $user_id,
						)
					));

		if(0 == count($booking)) die('Invalid Visit ID');
		
		$data['status']['text'] = 'contractor on the way';
		$data['status']['code'] = 4;

		$message = "The engineer is on the way to you now.";

		//need to notify the customer

		if($booking->save($data)) $this->redirect("/contractors/viewjob/$visit_id/");
	
	}

	public function startvisit($visit_id) {

	$this->secure('contractor');

	$user_id = $this->get_user_id();

		$booking = Bookings::find('first', array(
					'conditions' => array(
						'_id' => $visit_id,
						'contractor_id' => $user_id,
						)
					));

		if(0 == count($booking)) die('Invalid Visit ID');
		
		$data['status']['text'] = 'job is in progress';
		$data['status']['code'] = 5;
			
			$i = count($booking['timesheet']);	
			$data['timesheet'] = $booking['timesheet'];

		$data['timesheet'][] = array('start' =>  new MongoDate());

		if($booking->save($data)) $this->redirect("/contractors/viewjob/$visit_id/");
	}


	public function pausevisit($visit_id) {

	$this->secure('contractor');

	$user_id = $this->get_user_id();

		$booking = Bookings::find('first', array(
					'conditions' => array(
						'_id' => $visit_id,
						'contractor_id' => $user_id,
						)
					));

		if(0 == count($booking)) die('Invalid Visit ID');
		
		$data['status']['text'] = 'job is paused';
		$data['status']['code'] = 6;

			$i = count($booking['timesheet']) - 1;		
			$data['timesheet'] = $booking['timesheet'];

		$start_time = $booking['timesheet'][$i]['start'];
		$finish_time = new MongoDate();

		$data['timesheet'][$i]['finish'] = $finish_time;

		$minutes = ($finish_time->sec - $start_time->sec) / 60;
		
		$data['timesheet'][$i]['minutes'] = $minutes; 

		if($booking->save($data)) $this->redirect("/contractors/viewjob/$visit_id/");
	}


	public function updatejob($visit_id, $status, $nonce) {

	$this->secure('contractor');

	$user_id = $this->get_user_id();

		if('accepted' == $status) {


		}
	
	}


	public function settings() {

	    $user = Session::read('default');
        if ($user==""){ return $this->redirect('/login'); }


	if($user['permissions']['contractor'] != true) return $this->redirect('customers::index');

	$title = 'Contractor Settings';


	/*
		BELOW IS HERE AS A CONVENIENT PLACE TO ADD THE DEFAULT SERVICES

		DELETE WHEN READY

		$trades = array('Plumbing', 'Electrical', 'Building', 'Labourers');

		$categories = array(
					'General Plumbing',
				   	'Central Heating',
					'Boilers',
					'Drainage'
				);

		$default_services['General Plumbing'] = array('Taps', 'Toilets', 'Macerators', 'Leaks', 'Trace & Repair', 'Blocakages', 'Pumps', 'Electric Showers', 'Thermostatic Showers', 'Water Tanks');
		$default_services['Central Heating'] = array('Radiators', 'Vented Cylinders', 'Emersion Heaters', 'Unvented Cylinders');
		$default_services['Boilers'] = array('New Installations', 'Fault Finding', 'Annual Service', 'Gas Safety Certificate');
		$default_services['Drainage'] = array('High Pressure Jetting', 'CCTV Surveys', 'Repairs');

		foreach($categories as $category) {
		$data["Plumbing.$category"] = $default_services[$category];
		}

	//$services = Services::create()->save($data);
*/
	return compact('title');	
	}

	public function faqs() {

	$this->secure();

        if(! $this->is_contractor()) { return $this->redirect('/login'); }


	$title = 'Contractor FAQs';

	return compact('title');	
	}


}

?>
