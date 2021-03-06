<?php

require_once '../header_rest.php';
$controllerUser = new ControllerUser();

$email   = !empty($_GET["email"]) ? $_GET["email"] 	: "wrongemailaddress";
$user_id = !empty($_GET["user_id"]) ? $_GET["user_id"] : "wrongemailaddress";

$user = $controllerUser->isActive($email, $user_id);

if($user) {
	echo json_encode(["active" => "1"]);
}else{
	echo json_encode(["active" => "0"]);
}