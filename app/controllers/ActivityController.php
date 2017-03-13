<?php
namespace app\controllers;

use app\models\Actions;

class ActivityController extends \app\extensions\action\Controller {

	public function office($type = '') {

	$this->secure('staff');

		$title = 'Activity Timeline';

		return compact('title');
	}

}
?>
