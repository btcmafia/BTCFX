<?php
namespace app\controllers;

use app\models\Cronjob;
use app\models\Contractors;
use app\models\Timeslots;

use app\models\Testing;
use app\models\TestBook;



class CronjobController extends \app\extensions\action\Controller {

        public function index() {

		$this->secure('staff');

	die('Invalid');
        }


	public function create() {
	
		$this->secure('staff');

		$test = Testing::create();


			$bookings['cat'] = array('job_ref' => 'pegging', 'rate' => '12000', 'length' => 60*60);
			$bookings['dog'] = array('job_ref' => 'bondage', 'rate' => '19000', 'length' => 60*90);


			$array = array(
				'name' => 'Miss Victoria',
				'position' => 'Mistress',
				'bookings' => $bookings,
				);

		$foo = $test->save($array);

		$id = $test->_id;

		return "ID: $id";

	return 'done';
	}




	public function uj83hrwuisdui86236($days_ahead = 0) {

	if($days_ahead < 3) $days_ahead = 3;

              $contractors = Contractors::find('all', array(
                                     'conditions' => array(
                                       		'active' => true,
                                      )
					 ));

		if(count($contractors) == 0) return('No contractors found');

//die('BOOM!');

	$time_now = time();

	$created = false;

	//loop through contractors checking if each timeslot exists from today until $days_ahead
	//if it doesn't we create it
	for( $count = 0; $count <= $days_ahead; $count++) {

		foreach($contractors as $contractor) {

			$time_period = $time_now + (60 * 60 * 24 * $count);

			$day = date('z', $time_period) + 1;
			$week = date('W', $time_period);
			$year = date('Y', $time_period);
	

			$check = Timeslots::find('all', array(
					'conditions' => array(
						'user_id' => $contractor['user_id'],
						'period.day' => $day,
						'period.week' => $week,
						'period.year' => $year,
						)
					));

		if(0 == count($check)) {

		$created = true;

		$i = array('Morning', 'Lunchtime', 'Afternoon', 'Evening'); 
		
		foreach($i as $key => $period_nicename) {

			$timeslot = Timeslots::create();

			$data = array(
				'user_id' => $contractor['user_id'],
				'trading_name' => $contractor['trading_name'],
				'period.day' => $day,
                                'period.week' => $week,
                                'period.year' => $year,
				'period.order' => $key,
				'period_nicename' => $period_nicename,
				'slots_available' => $contractor['default_jobs_per_period'][$period_nicename],
				'default_max' => $contractor['default_max_jobs_per_period'][$period_nicename],
				'rate' => $contractor['default_rate'][$period_nicename], 
				'edited' => false
				);

			$timeslot->save($data);
		}
		}
		}
		}

	if(! $created) die('<h1>No Timeslots Required</h1><p><a href="/">Click here to continue.<a></p>');

	die('<h1>Timeslots created</h1><p><a href="/">Click here to continue.<a></p>');
	}


	public function schedule($day = 0) {

        $this->secure();
        $user_id = $this->get_user_id();

        $title = 'My Availability';

	      //get slots for the requested day
              $timeslots = Timeslots::find('all', array(
                                     'conditions' => array(
                                       		'user_id' => $user_id,
                                        	'day' => $today)
                                       ));

		if(count($timeslots) == 0) return 'Schedule not found';

		//should return 4 timeslots for the day
		foreach($timeslots as $timeslot) {

		$periods[] = array('slots_available' => $timeslot['slots_available'], 'period_nicename' => period_nicename($timeslot['period']), 'period_details' => period_details($timeslot['period']));
		}



	

	return $this->render(['layout' => 'mobile'], compact('period'));

	}

}

?>
