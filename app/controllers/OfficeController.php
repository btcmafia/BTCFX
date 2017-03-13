<?php
namespace app\controllers;

use app\models\Timeslots;
use app\models\Contractors;
use app\models\Services;
use app\models\Customers;
use app\models\Emails;
use app\models\Addresses;
use app\models\Jobs;
use app\models\Bookings;
use app\models\Users;
use app\models\ActiveData;
use app\models\Nonces;
use MongoID;
use MongoDate;

use lithium\storage\Session;

use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\Money;
use app\extensions\action\XJobs;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;



class OfficeController extends \app\extensions\action\Controller {

        public function index() {

        $this->secure('staff');

	return 'Manage Contractors Schedule';
        }

	public function testing() {

	return;
	}

	public function schedule($page = false, $service_required = false) {

        $this->secure('staff');
        $user_id = $this->get_user_id();

	$active_data = $this->get_active_data();

//	print_r($active_data['active_service']);
echo '&nbsp'; //very strange, unless we echo something it doesn't save the active_service when it changes!!!!
        $title = 'Contractors Availability';

			$active_customer_id = $active_data['active_customer_id'];
			$active_customer = $active_data['active_customer'];
		
			$active_service_address_id = $active_data['active_service_address_id'];
			$active_service_address = $active_data['active_service_address'];

			if( (! is_numeric($page)) && ($active_data->schedule_day != '')) { $page = $active_data->schedule_day; }
			elseif( is_numeric($page) ) { $foo['schedule_day'] = $page; }
			else { $page = 0; $foo['schedule_day'] = 0; }
	
			if('' == $service_required) $service_required = $active_data['active_service'];


	
		//get all the active services for the select menu
		$all_services = Services::find('all', array(
					'conditions' => array(
						'active' => true,
						)
					));

				$service_selected = false;

//echo "<p>Service Required: $service_required</p>";
	
		$service_required = urldecode($service_required);
		
//echo "<p>Service Required: $service_required</p>";
		
		foreach($all_services as $service) {

			$services[$service['service_name']] = array('service_id' => (string) $service['_id']);

				if($service_required == $service['service_name']) { 

					$services[$service_name]['selected'] = true;
					$service_selected = true;
					$foo['active_service'] = $service_required;

				}
		}

//print_r($service_selected);
	
	if(false == $service_selected) {

				$service_required = false; // no valid result found
				$foo['active_service'] = '';
	}

//echo "<h2>Foo:<h2>";	
//print_r($foo);
//die;

	$this->update_active_data($foo);




	//only show timeslots if we have a valid service selected
	if($service_required) {

		$search_time = time() + (60*60*24*$page); 

		$search_day = date('z', $search_time) + 1; //+1 because date('z') starts counting from zero
		$search_year = date('Y', $search_time);

		$day_name = date('l',  $search_time); //passed to the view

			//find contractors ready to go
			$contractors = Contractors::find('all', array(
					'conditions' => array(
					'ready_to_go' => true,
						)
					));
	
		foreach($this->get_periods() as $period) {
	
		$timeslots[$period] = Timeslots::find('all', array(
                	                     'conditions' => array(
                        	                	'period.day' => $search_day,
							'period.year' => $search_year,
							'period_nicename' => $period,
							'slots_available' => array('$gt' => 0),
							)
					 		));


		
			if(count($timeslots[$period]) == 0) {
							     $available_contractors[$period] = false;
							     continue;
							   }


		$i = 0;

		//check they offer this service, get the contractors rates and quality score
		foreach($timeslots[$period] as $slot_details) {


			$contractor = Contractors::find('first', array(
						'conditions' => array(
							'user_id' => $slot_details['user_id'],
							"services.$service_required.active" => true,
						//	'areas' => array('$in' => $area_required),
							)
							));
/*
echo '<pre>';
print_r($contractor['services']);
echo '</pre>';
die;
*/
		if(0 == count($contractor)) { 
						//$available_contractors[$period][$i] = false;
						continue;
					   }

		$money = new Money();

		$available_contractors[$period][$i] = array(
							'timeslot_id' => $slot_details['_id'],
							'contractor_id' => $contractor['user_id'],
							'trading_name' => $contractor['trading_name'], 
							'rate' => 2 * $money->display_money($contractor['default_rate'][$period], 'tcp'),
							'detailed_rate' => $contractor['detailed_rate'][$area][$service][$period],
							'quality_score' => $contractor['quality_score'], //todo make quality score service dependent
							'ave_job_time' => $contractor['ave_job_time'][$service_required],
							'slots_available' => $slot_details['slots_available'],
							'jobs_booked' => $slot_details['jobs_booked'],
							);


		//determine the actual rate
		if    ('' == $available_contractors[$period][$i]['ave_job_time']) $available_contractors[$period][$i]['estimated_cost'] = '';

		elseif('' == $available_contractors[$period][$i]['detailed_rate']) {

			 $available_contractors[$period][$i]['estimated_cost'] = $available_contractors[$period][$i]['rate'] * $available_contractors[$period][$i]['ave_job_time'];
		} 
		else {

			$available_contractors[$period][$i]['estimated_cost'] = $available_contractors[$period][$i]['detailed_rate'] * $available_contractors[$period][$i]['ave_job_time'];
		}

		$i++;
		
	} //end foreach timeslot get contractor details

		if(0 == count($available_contractors[$period])) $available_contractors[$period] = false;
//return ("Count is $i");
	} //end get timeslots

	} //end if valid service selected


		return compact('available_contractors', 'service_required', 'area_nicename', 'slot_user_id', 'page', 'day_name', 'services', 'active_customer_id', 'active_customer', 'active_service_address_id', 'active_service_address');

	}


	public function assignvisit($timeslot_id = null) {

		$this->secure('staff');

			if($this->request->data) {

				$timeslot_id = (string) $this->request->data['timeslot_id'];

					//validates timeslot and contractor in one go
					//
					$timeslot = Timeslots::find('first', array(
								'conditions' => array(
									'_id' => $timeslot_id,
									'user_id' => $this->request->data['contractor_id'],
									)
								));

					if(0 == count($timeslot)) die('Invalid timeslot or contractor');



					$customer = Customers::find('first', array(
								'conditions' => array(
									'_id' => $this->request->data['customer_id'],
									)
								));

					if(0 == count($customer)) die('Invalid customer');

					$service_address_id = $this->request->data['service_address_id'];

					//validate service address
					if(! isset($customer['service_addresses'][$service_address_id]) ) die('Invalid service address');


					//the rate info was stored as the nonce
					//it means we honour the price quoted even if it's changed since
					$nonce = Nonces::find('first', array(
								'conditions' => array(
									'_id' => $this->request->data['nonce'],
									'customer_id' => $this->request->data['customer_id'],
									'timeslot_id' => $timeslot_id,
									)
								));


					if(0 == count($nonce)) die("Invalid nonce"); 

					//prices quoted are valid for 15 minutes
					//if it's expired we'll check if the price has changed
					if( $nonce['expiry'] < time() ) {

						if( ($nonce['rate'] != $timeslot['rate'])
						OR  ($nonce['min_charge'] != $timeslot['min_charge']) ) {
		
					$time_now = time();
					/*
					echo "<p>Nonce ID: $nonce->_id<br />
						Expiry: {$nonce['expiry']}< />
						Time Now: $time_now</p>";
					die;
					*/
						return $this->redirect("/office/quoteexpired/{$nonce->_id}");
						}
					} 
					
					$rate = $nonce['rate'];
					$min_charge = $nonce['min_charge'];


				$service_required = urldecode($this->request->data['service_required']);
				$service_id = $this->request->data['service_id']; //not currently used
				
				$service = $customer['service_addresses'][$service_address_id];
				
				$service_address = $this->format_address($service);
				$service_address_html = $this->format_address($service, $html); 

				
			
			//if we have a job_id then add the visit to that job
			if('' != $this->request->data['job_id']) {

				$job = Jobs::find('first', array(
						'conditions' => array(
							'_id' => $this->request->data['job_id'],
							)
						));

				if(0 == count($job)) die('Invalid job ID');

			}
			else { //create a new job

				$job = Jobs::create();
			
				$data['job_title'] = $this->request->data['job_description']; //use the job_description for both visit and job title, for now
				$data['job_status'] = 'initiated';
		
				$job->save($data);
			}
				

		$data['_id'] = (string) $job['_id'];
		$data['customer_id'] = (string) $customer['_id'];
	
		
		$visit['job_id'] = $data['_id'];
		$visit['customer_id'] = $data['customer_id'];

		$visit['customer']['name'] = $customer['first_name'] . ' ' . $customer['last_name'];
		$visit['customer']['email'] = $customer['email'];
		$visit['customer']['phone'] = $customer['phone'];

		$visit['billing_address']['address_1'] = $customer['address_1'];
		$visit['billing_address']['address_2'] = $customer['address_2'];
		$visit['billing_address']['city'] = $customer['city'];
		$visit['billing_address']['postcode'] = $customer['postcode']; 
	

		$visit['visit_title'] = $this->request->data['job_description'];
	
			if('' != $this->request->data['notes']) {	
				$permissions = array('customer' => true, 'office' => true, 'contractor' => true);

			$note_id = (string) new MongoID();

		$visit['notes'][$note_id] = array('date' => new \MongoDate(), 'author' => 'office', 'note' => $this->request->data['notes'], 'permissions' => $permissions);
		
			}


		$visit['service_required'] = $service_required;

		$visit['service_address']['contact_name'] = $service['contact_name'];
		$visit['service_address']['phone'] = $service['phone'];
		$visit['service_address']['email'] = $service['email'];
		$visit['service_address']['address_1'] = $service['address_1'];
		$visit['service_address']['address_2'] = $service['address_2'];
		$visit['service_address']['city'] = $service['city'];
		$visit['service_address']['postcode'] = $service['postcode']; 
	

		//Todo: test this and use DateTime instead

			$period_start_time = $this->period_details($timeslot['period_nicename'], 'start_time');
			$period_end_time = $this->period_details($timeslot['period_nicename'], 'end_time');

//echo "Start: $period_start_time<br />";
//echo "Finish: $period_end_time<br />";

		$visit['timeslot_id'] = (string) $timeslot['_id'];

			$timeslot_unix_time = mktime(1, 1, 1, 1, $timeslot['period']['day'], $timeslot['period']['year']);

		$visit['time_and_date']['day'] = date("l", $timeslot_unix_time);
		$visit['time_and_date']['date'] = date("jS F Y", $timeslot_unix_time);
		$visit['time_and_date']['period'] = $timeslot['period_nicename'];

		$visit['time_and_date']['nicename'] =  date("l jS F Y", $timeslot_unix_time);
		$visit['time_and_date']['nicename'] .= " between $period_start_time and $period_end_time";


//print_r($visit['time_and_date']);
//die;

		$visit['contractor_id'] = $timeslot['user_id'];
		$visit['contractor_name'] = $timeslot['trading_name'];
		$visit['rate'] = $rate;
		$visit['min_charge'] = $min_charge;

		$visit['status']['code'] = 0;
		$visit['status']['text'] = 'pending customer approval';

	$booking = Bookings::create();
		
		$booking->save($visit);
		$nonce->delete();

		//easy access for email template
		$visit['comments'] = $visit['notes'][$note_id]['note'];
		$visit['service_address']['address'] = $this->format_address($visit['service_address']);
		$visit['billing_address'] = $this->format_address($visit['billing_address']);	
		$job_id = $job['_id'];
		$visit_id = $booking['_id'];	
	
			$money = new Money();

		$visit['rate'] = $money->display_money($rate, 'tcp');
		$visit['min_charge'] = $money->display_money( $min_charge, 'tcp');

		//send confirmation email
		               $view  = new View(array(
                                'loader' => 'File',
                                'renderer' => 'File',
                                'paths' => array(
                                        'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
                                )
                        ));

                        $body = $view->render(
                                'template',
                                compact('job_id', 'visit_id', 'visit', 'data'),
                                array(
                                        'controller' => 'office',
                                        'template'=>'CustomerAcknowledgement',
                                        'type' => 'mail',
                                        'layout' => false
                                )
                        );
                        $transport = Swift_MailTransport::newInstance();
                        $mailer = Swift_Mailer::newInstance($transport);

                        $message = Swift_Message::newInstance();
                        $message->setSubject('Confirm your tradesperson appointment');
                        $message->setFrom(array(NOREPLY => COMPANY_NAME));
                        $message->setTo($visit['customer']['email']);
                        $message->setBody($body,'text/html');

                        if(! $foo = $mailer->send($message) ) {

			die("IMPORTANT: The job has been created but we failed to send the customer confirmation email. Please report this problem ASAP.");
			}


		return $this->redirect("/office/jobbooked/$job_id/");		
		exit;	
			} //end POST data received

		$active_data = $this->get_active_data();
		
	                $active_customer_id = $active_data['active_customer_id'];

					if($active_data['active_service'] == '') {
					return $this->redirect('/office/schedule/');
					exit;
					}

				$customer = Customers::find('first', array(
							'conditions' => array(
								'_id' => $active_customer_id,
								)
							));

				if(0 == count($customer)) die('Invalid Customer');


			$this->set( array('service_required' => $active_data['active_service']) );
			
			$this->set( array('customer_id' => $active_customer_id) );
			$this->set( array('customer_name' => $customer->first_name . ' ' . $customer->last_name) );
			$this->set( array('customer_phone' => $customer->phone) );
			$this->set( array('customer_email' => $customer->email) );

			$this->set( array('service_address' => $active_data['active_service_address']) );
			$this->set( array('service_address_id' => $active_data['active_service_address_id']) );

				$address = $customer['service_addresses'][$active_data['active_service_address_id']];

			$this->set( array('service_address_contact_name' => $address['contact_name']) );
			$this->set( array('service_address_phone' => $address['phone']) );
			$this->set( array('service_address_email' => $address['email']) );
                        

				$timeslot = Timeslots::find('first', array(
							'conditions' => array(
								'_id' => $timeslot_id,
								)
							));

				if(0 == count($timeslot)) die('Invalid Timeslot');


			$this->set( array('timeslot_id' => (string)$timeslot_id) );

			$this->set( array('contractor_name' => $timeslot['trading_name']) );
			$this->set( array('contractor_id' => $timeslot['user_id']) );

				$rate = $timeslot['rate'] * 2;
				$min_charge = $rate;


				//we store the rate info so we can honour it for 15 minutes even if it changes
				$new_nonce = Nonces::create();

				$ma = array(
						'rate' => $rate,
						'min_charge' => $min_charge,
						'customer_id' => (string)$active_customer_id,
						'timeslot_id' => $timeslot_id,
						'expiry' => time() + 60 * 15,
					);

				$new_nonce->save($ma);
			
			$this->set( array('nonce' => (string) $new_nonce['_id']) );

				$money = new Money();
				
				$rate = $money->display_money($rate, 'tcp');
				$min_charge = $money->display_money($min_charge, 'tcp');

			$this->set( array('rate' => $rate)  );  	
			$this->set( array('min_charge' => $min_charge) );  	

		return compact('active_data');	

	}


	public function newserviceaddress() {

        $this->secure('staff');
	
		if($this->request->data) {

			$customer = Customers::find('first', array(
					'conditions' => array(
						'_id' => $this->request->data['customer_id'],
						)
					));


			if(count($customer) == 0) {
				$error = 'Invalid customer ID';

			return $this->request->data['customer_id'];

			die('Error'); //compact('error init');
			}

			$service_address_id = new MongoID();

			$service['contact_name'] = $this->request->data['contact_name'];
			$service['address_1'] = $this->request->data['address_1'];
			$service['address_2'] = $this->request->data['address_2'];
			$service['city'] = $this->request->data['city'];
			$service['postcode'] = $this->request->data['postcode'];
			$service['phone'] = $this->request->data['phone'];
			$service['email'] = $this->request->data['email'];
			

			//ToDo:
			//implement default addresses
			//$is_default = ($this->request->data['make_default'] == 1 ? true : false);
			//$service['is_default'] = $is_default;

		$data = array(
				 "service_addresses.$service_address_id" => $service,
			     );

		$customer->save($data);	

				

			//make the customer and address "active"
			$cookie_id = Session::read('active_data');
			if('' == $cookie_id) { $cookie_id = new MongoID(); Session::write('active_data', array('value' => $cookie_id)); } 


				$foo = ActiveData::find('first', array(
					'conditions' => array(
							'_id' => $cookie_id
							)
						));

				if(! $foo) $foo = ActiveData::create();

				$bar['active_customer_id'] = $customer->_id;
				$bar['active_customer'] = "{$customer->first_name} {$customer->last_name}";
				$bar['active_service_address_id'] = $service_address_id;
				$bar['active_service_address'] = "{$service['address_1']}, {$service['address_2']}, {$service['postcode']}";

				$foo->save($bar);				

				return $this->redirect("/office/viewcustomer/$customer->_id");
				}
		}


	public function selectserviceaddress() {

	$this->secure('staff');

		if($this->request->data) {

			
			$customer = Customers::find('first', array(
					'conditions' => array(
			//			'_id' => $this->request->data['customer_id'],
							"service_addresses.{$this->request->data['service_address_id']}" => array('$exists' => true),
						)
					));

			if(count($customer) == 0) {

				die("No results");
			}


				$service_address_id = $this->request->data['service_address_id'];

					$address_1 = $customer["service_addresses.$service_address_id.address_1"]; 
					$address_2 = $customer["service_addresses.$service_address_id.address_2"]; 
					$postcode = $customer["service_addresses.$service_address_id.postcode"];
				
				$active_service_address = "$address_1, $address_2, $postcode";

				$bar['active_customer_id'] = $customer->_id;
				$bar['active_customer'] = "{$customer->first_name} {$customer->last_name}";
				$bar['active_service_address_id'] = $service_address_id;
				$bar['active_service_address'] = $active_service_address;

				$this->update_active_data($bar);				

				return $this->redirect("/office/schedule/");

	}
	}

	public function clearactivecustomer() {

		$this->secure('staff');

				$bar['active_customer_id'] = '';
				$bar['active_customer'] = '';
				$bar['active_service_address_id'] = '';
				$bar['active_service_address'] = '';

				$this->update_active_data($bar);

				return $this->redirect("/office/schedule/");
				
	}

	public function viewcustomer($customer_id) {

        $this->secure('staff');
	
		$customer = Customers::find('first', array(
				'conditions' => array(
					'_id' => $customer_id
					)
				));

		if($customer) {

			$active_service_address_id = $this->get_active_data('active_service_address_id');

			return compact('customer', 'active_service_address_id');
		}

		else die("Invalid Customer ID");
	}


	public function searchcustomers() {

        $this->secure('staff');
	
		if($this->request->data) {

			$query = $this->request->data['query'];
			$field = $this->request->data['field'];

			//explanation at http://old.shift8creative.com/blog/mongodb-queries-with-lithium-part-three.html
			$search_regex = new \MongoRegex('/'.$this->request->data['query'].'/i');

			$results = Customers::find('all', array(
					'conditions' => array(
						$field => $search_regex,
						)
					));

			$count = count($results);

			return compact('results', 'query', 'field', 'count');
		}

	}

	public function newcustomer() {

        $this->secure('staff');

		if($this->request->data) {

			$customer = Customers::create();
	
			$first_name = $this->request->data['first_name'];
			$last_name = $this->request->data['last_name'];
			$email = $this->request->data['email'];
			$phone = $this->request->data['phone'];
			$address_1 = $this->request->data['address_1'];
			$address_2 = $this->request->data['address_2'];
			$city = $this->request->data['city'];
			$postcode = $this->request->data['postcode'];

			//if service address given then use that otherwise use billing address
			$use_billing = ($this->request->data['use_billing'] == '1' ? true : false);

			$service_address_id = new MongoID();
			$service['contact_name'] = ($use_billing ? $first_name . ' ' . $last_name : $this->request->data['service_contact_name']);  
			$service['address_1'] = ($use_billing ? $address_1 : $this->request->data['service_address_1']);  
			$service['address_2'] = ($use_billing ? $address_2 : $this->request->data['service_address_2']);  
			$service['city'] = ($use_billing ? $city : $this->request->data['service_city']);  			
			$service['postcode'] = ($use_billing ? $postcode : $this->request->data['service_postcode']);  				
			$service['email'] = ($use_billing ? $email : $this->request->data['service_email']);  
			$service['phone'] = ($use_billing ? $phone : $this->request->data['service_phone']);  


			$data = array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $email,
					'phone' => $phone,
					'address_1' => $address_1,
					'address_2' => $address_2,
					'city' => $city,
					'postcode' => $postcode,
					"service_addresses.$service_address_id" => $service,
				);

				if($customer->save($data)) {

				$success = true;

				$customer_id = $customer->_id;

			
			//Update or create the active_data cookie with the customer_id and service_address_id

			if(! $cookie_id = Session::read('active_data')) {

				$session = ActiveData::find('first', array(
						'conditions' => array(
							'cookie_id' => $cookie_id
							)
						));
			}


			if( (1 != count($session)) OR ('' == $cookie_id) ) {

				$session = ActiveData::create();
			}

			$session->save( array('active_customer' => $customer_id, 'active_service_address_id' => $service_address_id) );
			$cookie_id = $session->_id;

			Session::write('active_data', $value = $cookie_id); 

				return compact('success', 'customer', 'customer_id', 'service_address_id', 'service');
				}

			$errors = $customer->errors();

			return compact('customer', 'errors');
		}
	}


	public function jobbooked($job_id) {

	$this->secure('staff');

	$title = 'Job Booked';

	return compact('title', 'job_id', 'job');	
	}


	public function quoteexpired($nonce_id) {

	$this->secure('staff');

	$title = 'Quote Expired';

	return compact('nonce_id');
	}
	
	/*
	  we expect this as a javascript form submit so we just return json
	*/
	public function newcustomerandjob() {

	$this->secure('staff');

		if($data = $this->request->data) {

		$email = $this->request->data['email'];

		//check email address is not in use
		$check = Emails::find('first', array(
				'conditions' => array(
					'Email' => $email,
					'Verified' => true,
					)
				));

		if(count($check) > 0) {

		$success = false;
		$error = 'Email address is already in use';

		$customer = Customers::create();
		$customer->error('email');

		return compact('user');

	$errors = $user->errors();

		//todo: get the customer details and return them as a suggested customer

		//todo: what about non verified email addresses, should we delete them, or suggest the customer? 

		return $this->render(array('json' => compact('success', 'error')));
		}

		
		$service_id = $this->request->data['service_id'];

		//we create a new user
		$user = Users::create();

                $ga = new  GoogleAuthenticator();

                $username = $ga->createSecret(20); //hopefully large enough to prevent clashes! 
		$password = $ga->createSecret(28); 
                $firstname = $this->request->data['first_name'];
                $lastname = $this->request->data['last_name'];
                $phone = $this->request->data['phone'];
                $postcode = $this->request->data['postcode'];
		$address_1 = $this->request->data['address_1'];
		$address_2 = $this->request->data['address_2'];
		$city = $this->request->data['city'];

                $permissions['admin'] = false;
                $permissions['office'] = false;
                $permissions['contractor'] = false;
                $permissions['customer'] = true;


                $data = array(
                        'username' => $username,
			'password' => $password,
			'password2' => $password,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email,
                        'permissions' => $permissions,
                        );

                $saved = $user->save($data);
               
 
		if($saved==true){
		
		//and a customer with the user_id
		$customer = Customers::create();
               
			$new_user_id = (string) $user->_id;

			$data = array(
					'user_id' => $new_user_id,
					'first_name' => $firstname,
					'last_name' => $lastname,
					'email' => $email,
					'phone' => $phone,
					'address_1' => $address_1,
					'address_2' => $address_2,
					'city' => $city,
					'postcode' => $postcode,
				);

		$customer->save($data);


            //generate a password reset key
                $key = $ga->createSecret(64);
                $expiry = time() + 60 * 60 *24 * 7; //7 days


                        $data = array(
                                'user_id'=>$new_user_id,
                                'username'=>$username,
                                'email.verified'=> "Yes", //always because they are stored seperately now
                                'email.verify' => $verify_code,
                                'PasswordReset.Key' => $verify_code,
                                'PasswordReset.Expiry' => $expiry,
                                'key'=>$ga->createSecret(64),   //not sure we still need this?
                                'secret'=>$ga->createSecret(20), //or this
                                'balance.BTC' => (int)0,
                                'balance.TCP' => (int)0,
                                'balance.DCT' => (int)0,
                        );

		$verify_code = $ga->createSecret(24);

		       $emaildata = Emails::create(array(
                                'user_id'    => $new_user_id,
                                'Email'      => $email,
                                'VerifyCode' => $verify_code,
                                'Verified'   => false, 
                                'Default'     => true,
                                ))->save();


		
		//now create a new job
		$jobs = new XJobs();
		$job = $jobs->new_job($customer);

		$job_id = (string) $job->_id;

		$active_customer = "$first_name $last_name. $address_1 $postcode";

		$active_data = array(
					'job_id' => $job_id,
					'customer_id' => $new_user_id,
					'service_id' => $service_id,
				    );	

		

		//todo:learn how built in session management works
			//Session::write('active_data', $active_data);

		setcookie('active_data', $active_data, 0, '/');

		return $this->redirect('/office/customercreated');

		exit;

		 return $this->render(array('json' => compact('success', 'active_customer', 'job_id')));
		}
		else { //$saved!=true

	$errors = $user->errors();


print_r($errors);
die;
		$success = 'Failed miserably!';

		 return $this->render(array('json' => compact('success', 'active_customer', 'job_id')));
			
		}
		
		}
	}

		

}
?>
