<?php

namespace app\extensions\action;

use app\models\Visits;

class Visits extends \lithium\action\Controller{


       public function __construct() {

        return;
        }

        public function new_visit($job_id, $service, $timeslot, $contractor_id = false) {

		$visit = Visits::create();

		$data = array(
				'job_id' => $job_id,
				'service' => $service,
			);

		if($contractor_id) $data['contractor_id'] = $contractor_id;

		$visit->save($data);

		return $visit->_id; //no idea if this works!!
        }

	public function assign_contractor($visit_id, $contractor_id, $rate, $status = false) {


                        $visit = Visits::find('first', array(
                                'conditions' => array(
					'visit_id' => $visit_id
					) 
                                ));

                //return false if not found
                if(0 == count($visit)) return false;

		if(! $status) $status = 'initiated';

		$data = array('contractor_id' => $contractor_id,
			      'rate' => $rate,
			);

		$visit->save($data);

	}

        public function cancel_visit($visit_id) {

		$data['status'] = 'cancelled';

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ))->save($data);

        }

	public function customer_accept_visit($visit_id, $customer_id) {

		$data['status'] = 'customer_accepted';

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ))->save($data);

	}

	public function contractor_accept_visit($visit_id, $contractor_id) {

		$data['status'] = 'contractor_accepted';

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ))->save($data);

	}

	public function customer_reject_visit($visit_id, $customer_id) {

		$data['status'] = 'customer_rejected';

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ))->save($data);

	}

	public function contractor_reject_visit($visit_id, $contractor_id) {

		$data['status'] = 'contractor_rejected';

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ))->save($data);
	}

	public function start_visit($visit_id, $location) {

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ));

		$start_time = time();

		$count = count($visit['timings']);

		$data['timings'][$count]['start'] = $start_time;
		$data['timings'][$count]['location'] = $location;

		$visit->save($data);
	}
	
	public function pause_visit($visit_id, $location) {

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ));

		$count = count($visit['timings']) - 1;

		if($count < 0) return false; //no visit started
		
		$pause_time = time();
		$minutes = $pause_time - $visit['timings'][$count]['start'];


		$data['timings'][$count]['finish'] = $pause_time;
		$data['timings'][$count]['location'] = $location;
		$data['timings'][$count]['minutes'] = $minutes;

		$visit->save($data);

	}
	
	public function finish_visit($visit_id, $location, $status) {

                $visit = Visits::find('first', array(
                                'conditions' => array(
                                        'visit_id' => $visit_id
                                        ) 
                                ));

		$count = count($visit['timings']) - 1;

		if($count < 0) return false; //no visit started
		
		$finish_time = time();
		$minutes = $finish_time - $visit['timings'][$count]['start'];

		$data['status'] = $status;
		$data['timings'][$count]['finish'] = $finish_time;
		$data['timings'][$count]['location'] = $location;
		$data['timings'][$count]['minutes'] = $minutes;

		$visit->save($data);

	}

  
        public function add_note($visit_id, $author_id, $author_role, $note) {


        }

        public function edit_note($visit_id, $note) {


        }

        public function delete_note($visit_id) {


        }

	public function notify_visit_running_late($visit_id, $how_late, $reason) {


	}
}

