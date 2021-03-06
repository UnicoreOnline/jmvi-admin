<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 20/2/18
 * Time: 1:17 AM
 */

require_once '../header_rest.php';
$controllerBid = new ControllerBid();
$controllerUser = new ControllerUser();
$controllerAuction = new ControllerAuction();
$controllerRest = new ControllerRest();
$controllerRealEstate = new ControllerRealEstate();


//file_put_contents("log.txt",json_encode($_POST));


$login_hash = "";
if (!empty($_POST['login_hash']))
    $login_hash = $_POST['login_hash'];

$user_id = "";
if (!empty($_POST['user_id']))
    $user_id = $_POST['user_id'];

$auction_id = 0;
if (!empty($_POST['auction_id']))
    $auction_id = $_POST['auction_id'];

$name = "";
if (!empty($_POST['name']))
    $name = $_POST['name'];

$bid_amount = 0;
if (!empty($_POST['bid_amount']))
    $bid_amount = trim(strip_tags($_POST['bid_amount']));

$currency = "";
if (!empty($_POST['currency']))
    $currency = trim(strip_tags($_POST['currency']));

$is_deleted = 0;
if (!empty($_POST['is_deleted']))
    $is_deleted = $_POST['is_deleted'];

$api_key = "";
if (!empty($_POST['api_key']))
    $api_key = $_POST['api_key'];

$realestate_id = "";
if (!empty($_POST['realestate_id']))
    $realestate_id = $_POST['realestate_id'];

if (Constants::API_KEY != $api_key) {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => "4", 'status_text' => 'Invalid API Access Key.');
    echo json_encode($arrayJSON);
    return;
}

if (empty($login_hash) && $user_id == 0) {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'Invalid Access. User is missing login hash. Please relogin in the app.');
    echo json_encode($arrayJSON);
    return;
}

$is_valid = $controllerUser->isUserValid($user_id, $login_hash);
if (!$is_valid) {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => "6", 'status_text' => 'Invalid Access. Invalid login hash. Please relogin in the app.');
    echo json_encode($arrayJSON);
    return;
}

if ($is_deleted == 1 && $bid_id > 0) {
    $user = $controllerUser->getUserByUserId($user_id);
    $login_hash = str_replace(" ", "+", $login_hash);
    if ($user != null) {
        if ($user->login_hash == $login_hash) {
            $controllerRealEstate->deleteRealEstate($realestate_id, 1);
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Success.');
            $arrayJSON['realestate_info'] = array('realestate_id' => $realestate_id, 'is_deleted' => 1);
            echo json_encode($arrayJSON);
            return;
        } else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you are out of sync. Please relogin again.');
            echo json_encode($arrayJSON);
            return;
        }
    }
} else if ($realestate_id >= 0 && $user_id > 0) {
    $user = $controllerUser->getUserByUserId($user_id);
    if ($user == null) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you are out of sync. Please relogin again.');
        echo json_encode($arrayJSON);
        return;
    }
    if ($user->login_hash != $login_hash) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you are out of sync. Please relogin again.');
        echo json_encode($arrayJSON);
        return;
    }
    if($auction_id != ''){
        $auctionDetail = $controllerAuction->getAuctionByAuctionId($auction_id);
        if(!empty($auctionDetail) && $auctionDetail->is_start_bid == 0){
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems bid time not started yet.');
            echo json_encode($arrayJSON);
            return;
        }
    }

    $isAllowed = 0;
    if(!empty($auctionDetail)) {
        $isAllowed = $controllerBid->checkAccessForBid($auctionDetail->property_id,$user_id);
    }

    if ($isAllowed != 1) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'You are not allowed to bid on this property.');
        echo json_encode($arrayJSON);
        return;
    }
    
    $login_hash = str_replace(" ", "+", $login_hash);
    $realestate = $controllerRealEstate->getRealEstateByRealEstateId($realestate_id);

    
    $itm = new Bid();
    $itm->auction_id = $auction_id;
    $itm->user_id = $user_id;
    $itm->currency = $currency;
    $itm->bid_amount = $bid_amount;
    $itm->name = $name;
    $itm->created_at = date('Y-m-d H:i:s');
    $controllerBid->insertBid($itm);
    $bid_id = $controllerBid->getLastInsertedId();

    $results = $controllerRest->getResultRealEstateByRealEstateId($realestate_id);
    $objRealEstate = getObj($results);

    //$resultsPropertyType = $controllerRest->getPropertyTypeResultByPropertyTypeId($objRealEstate['property_type']);
    //$property_type = getObj($resultsPropertyType);
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Success.');
    $arrayJSON['realestate_info'] = $objRealEstate;
    //$arrayJSON['photos'] = $photosObj;
    //$arrayJSON['property_type_obj'] = $property_type;

    echo json_encode($arrayJSON);
} else {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => "3", 'status_text' => 'Invalid Access.');
    echo json_encode($arrayJSON);
}

function getArrayObjs($results)
{
    $ind = 0;
    $arrayObjs = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
            }
        }
        $arrayObjs[$ind] = $arrayObj;
        $ind += 1;
    }
    return $arrayObjs;
}

function getObj($results)
{
    $arrayObj = array();
    foreach ($results as $row) {
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
            }
        }
        break;
    }
    return $arrayObj;
}

?>