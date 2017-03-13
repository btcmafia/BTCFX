<?php

namespace app\extensions\action;

use app\models\Jobs;


class XJobs extends \lithium\action\Controller{

	public function __construct() {

	return;
	}

	public function new_job($customer) {

		$job = Jobs::create();

		$data = array(
                    		'customer_id' => $customer['user_id'],
		    		'status' => 'initiated',
				'DateCreated' => new \MongoDate(),
			     );

		$job->save($data);

	}

	public function get_job($job_id, $customer_id = false) {

		$conditions = array('job_id' => $job_id);
	
		if($customer_id) $conditions['customer_id'] = $customer_id;

			$job = Jobs::find('first', array(
				'conditions' => $conditions
				));
					
		return $job;

	}

	public function mark_job_complete($job_id, $customer_id = false) {

		$conditions = array('job_id' => $job_id);
	
		if($customer_id) $conditions['customer_id'] = $customer_id;
		
			$job = Jobs::find('first', array(
				'conditions' => $conditions
                                ));

		//return false if not found
		if(0 == count($job)) return false;

		$job->save(array('status' => 'completed'));

		return true;
	}

	public function delete_job($job_id, $customer_id = false) {

		$conditions = array('job_id' => $job_id);
	
		if($customer_id) $conditions['customer_id'] = $customer_id;
		
			$job = Jobs::find('first', array(
				'conditions' => $conditions
                                ));

		//return false if not found
		if(0 == count($job)) return false;

		$job->save(array('status' => 'deleted'));

		return true;

	}

	public function add_note($job_id, $author_id, $author_role, $note) {

		$conditions = array('job_id' => $job_id);
	
			$job = Jobs::find('first', array(
				'conditions' => $conditions
                                ));

		$count = count($job['notes']);

		$data = array( 
				"notes.$count.author_id" => $author_id,
				"notes.$count.author_role" => $author_role,
				"notes.$count.note" => $note
			);

		$job->save($data);

		return true;

	}

	public function edit_note($job_id, $note_id, $note) {

		$conditions = array('job_id' => $job_id);
	
			$job = Jobs::find('first', array(
				'conditions' => $conditions
                                ));

		$data = array( 
				"notes.$note_id.note" => $note
			);

		$job->save($data);

		return true;
		

	}

	public function delete_note($job_id) {

		return false;
	}


}

