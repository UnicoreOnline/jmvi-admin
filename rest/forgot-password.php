<?php
require_once '../header_rest.php';
require '../controllers/ControllerAuthentication.php';

if( isset($_POST['email']) ) {
	  
	  $controller = new ControllerAuthentication();
      $res = $controller->resetPasswordByEmail($_POST['email']);

      if($res) {
        $arrayJSON = array();
		$arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Your password has been reset and send email');
		echo json_encode($arrayJSON);
		return; 
      } else {
        $arrayJSON = array();
		$arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you had submmited wrong email.');
		echo json_encode($arrayJSON);
		return; 
      }
}
  
$arrayJSON = array();
$arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you had submmited wrong email.');
echo json_encode($arrayJSON);
return;