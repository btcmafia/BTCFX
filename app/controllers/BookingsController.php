<?php
/*
	The Bookings Controller is mainly used for viewing bookings because we can use the same code for staff, contractors and customers and just filter the results

	Assigning / updating bookings is done in the relevant Controller eg. Office, Contractors or Customers.
*/

namespace app\controllers;

use app\models\Bookings;

class BookingsController extends \app\extensions\action\Controller {

        public function index() {

		$title = "View Bookings";

	return compact('title');
	}


	public function view($status) {

	$this->secure();


		//if customer then restrict customer_id
		if( (! $this->is_contractor()) OR (! $this->is_staff()) OR (!$this->is_admin()) ) $customer_id = '123';

		//if not staff then only show contractor_id == user_id
		if( (! $this->is_staff()) OR (!$this->is_admin()) ) $contractor_id = $this->get_user_id();
		
		if($this->is_admin()) {

			$bookings = Bookings::find('all', array(
						'conditions' => array(
							'visit_status' => 'pending customer approval',
							)
						));

			return compact('title', 'bookings');
		}
	}

	public function contractor($contractor_id = '', $status = false) {
	
	$this->secure();
	$user_id = $this->get_user_id();

	$this->set(array('context' => 'contractors'));

	$title = 'Bookings';

	$conditions = array();



		if(! $this->is_staff() ) $contractor_id = $user_id;

		if( $this->is_staff() && $contractor_id != 'all' && $contractor_id != '') $conditions['contractor_id'] = $contractor_id;

		if($status) $conditions['visit_status'] = $status;

			$bookings = Bookings::find('all', array(
						'conditions' => $conditions 
						));

			foreach($bookings as $booking) {

			$booking['service_address']['address'] = $this->format_address($booking['service_address'], 'html');
			
			if($this->is_staff) $title = "Bookings - {$booking['contractor_name']}";
			}

			return compact('title', 'bookings');

	}



	public function office($filter) {

	$this->secure('staff');

	$this->set(array('context' => 'office'));

	$title = "Bookings - $filter";

		if('all' == $filter) {

		$conditions = array();
		}

		elseif('pending' == $filter) { //customer not confirmed

		$conditions = array('status.code' => 0);
		}

		elseif('unconfirmed' == $filter) { //contractor not accepted

		$conditions = array('status.code' => 1);
		}

		elseif('confirmed' == $filter) { //accepted by both so booked but not started

		$conditions = array('status.code' => 2);
		}

		elseif('ongoing' == $filter) { //in progress or paused

		$conditions = array('status.code' => 5);
		}

		elseif('paused' == $filter) { //contractor not accepted

		$conditions = array('status.code' => 6);
		}

		elseif('completed' == $filter) { //contractor has finished job. Follow up may or may not be required

		$conditions = array('status.code' => 7); // or 8
		}

		elseif('rebook' == $filter) { //contractor has finished job. Follow up booking required.

		$conditions = array('status.code' => 7);
		}

		elseif('closed' == $filter) { //Invoice has been sent. Booking is closed.

		$conditions = array('status.code' => 9); 
		}

		
			if(isset($conditions)) {

			$bookings = Bookings::find('all', array(
						'conditions' => $conditions 
						));

			foreach($bookings as $booking) {

			$booking['service_address']['address'] = $this->format_address($booking['service_address'], 'html');
			}

			}
			else { 
				$title = 'Bookings';
				$error = 'No results found';
				}
			
		return compact('title', 'bookings', 'error');
	}


}

?>
