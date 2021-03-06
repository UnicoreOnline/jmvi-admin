<?php
require '../header_rest.php';
$controllerRest = new ControllerRest();

$api_key = "";
if (!empty($_POST['api_key'])) {
    $api_key = $_POST['api_key'];
}

$country = "";
if (!empty($_POST['country'])) {
    $country = trim($_POST['country']);
}

if (Constants::API_KEY != $api_key) {
    $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access.');
    echo json_encode($arrayJSON);
    return;
}

$txt = json_encode($_POST);
$myfile = file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

$params = array(
    'country' => $country
);

$results = $controllerRest->searchPropetyByCountry($params);

$arrayJSON = array();
$arrayJSON['result_count'] = $results->rowCount();
$arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'Success.');
array_walk_recursive($arrayJSON, "convert_to_string");
echo json_encode($arrayJSON);

