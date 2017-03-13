<?php
namespace app\models;

class Customers extends \lithium\data\Model {


	public $validates = [
		'first_name' => [
			[
				'notEmpty',
				'required' => true,
				'message' => 'Customer first name is required.'
			]
		],
		'last_name' => [
			[
				'notEmpty',
				'required' => true,
				'message' => 'Customer last name is required.'
			]
		],

		'phone' => [
			[
				'notEmpty',
				'required' => true,
				'message' => 'Phone number is required.'
			]
		],
		'address_1' => [
			[
				'notEmpty',
				'required' => true,
				'message' => 'Address is required.'
			]
		],
		'postcode' => [
			[
				'notEmpty',
				'required' => true,
				'message' => 'A postcode is required.'
			]
		],
	];
}
?>
