<?php
require_once 'header.php';
$controller = new ControllerOrder();
$controllerReservedProperty = new ControllerReservedProperty();

$extras = new Extras();
if (isset($_POST) && !empty($_POST)) {
    
    $reservedProperty = $controllerReservedProperty->getReservedPropertyById($_POST['reserverd_property_id']);     
    
    $itm = new Order();    
    $itm->user_id = $reservedProperty->user_id;
    $itm->reserverd_property_id = $_POST['reserverd_property_id'];
    $itm->transaction_amount = $_POST['transaction_amount'];
    $itm->transaction_id = isset($_POST['related_resources'][0]['sale']['id']) ? $_POST['related_resources'][0]['sale']['id'] : '';
    $itm->transaction_status = isset($_POST['related_resources'][0]['sale']['state']) ? $_POST['related_resources'][0]['sale']['state'] : '';
    $itm->transaction_response = json_encode($_POST);
    $itm->created_at = time();
    
    $itemResult = $controller->insertOrder($itm);
    if ($itemResult) {
        $order_id = $controller->getLastInsertedId();
        if (!empty($order_id)) {
            
            
        }
    }
    $response = [
        'status' => '200',
        'message' => 'Order Added Successfully'
    ];
    echo json_encode($response);
    exit;
}
?>
