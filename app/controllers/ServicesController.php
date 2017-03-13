<?php
namespace app\controllers;

use app\models\Services;

class ServicesController extends \app\extensions\action\Controller {

        public function index() {

	$services = Services::find('all', array(
				'conditions' => array(
					'active' => true,
					)
			));

	return compact('services');
        }



}

?>
