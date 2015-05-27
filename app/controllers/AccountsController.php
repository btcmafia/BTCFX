<?php

/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2013, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace app\controllers;
use app\models\Users;
use app\models\Details;
use app\models\Pages;
use app\models\Parameters;
use lithium\data\Connections;
use MongoID;
use lithium\security\Auth;
use lithium\storage\Session;
use \lithium\template\View;

class AccountsController extends \lithium\action\Controller {
	public function index() {

	//return "Hello Aliens!";

	return; // $this->_render('element', 'elements/accounts');
	}

}
?>
