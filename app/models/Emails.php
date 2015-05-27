<?php
namespace app\models;

class Emails extends \lithium\data\Model {

	
public $validates = array(
    'Email' => array(
        array('email', 'message'=>'Not a valid email address.')
    )
);

}
?>
