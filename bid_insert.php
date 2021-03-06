<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 20/2/18
 * Time: 1:06 AM
 */

require_once 'header.php';
$controller = new ControllerBid();
$auctionController = new ControllerAuction();
$resalEstateController = new ControllerRealEstate();

$extras = new Extras();
if (isset($_GET)) {

    $itm = new Bid();

    $itm->auction_id = trim(strip_tags($_GET['auction_id']));
    $itm->user_id = trim(strip_tags($_GET['user_id']));
    $itm->name = trim(strip_tags($_GET['name']));
    $itm->currency = trim(strip_tags($_GET['currency']));
    $itm->bid_amount = trim(strip_tags($_GET['bid_amount']));
    $itm->created_at = time();

    $controller->insertBid($itm);

    $data = array();
    $bid = $controller->getLastInsertedId();
    $data['auction'] = $auctionController->getAuctionByAuctionId($bid['auction_id']);
    $data['property'] = $resalEstateController->getRealEstateByRealEstateId($data['auction']->property_id);
    echo json_encode($data);

}