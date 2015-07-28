<?php
namespace app\models;

class Invites extends \lithium\data\Model {

	
public $validates = array(
    'email' => array(

        array('email', 'message'=>'Not a valid email address.')
    )
);

}
?>
