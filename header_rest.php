<?php 
	$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
	if($contentType == "application/json"){
		$content = trim(file_get_contents("php://input"));
		$_POST   = json_decode($content, true);
        $_REQUEST   = json_decode($content, true);
	}
	require_once 'application/Globals.php';
	require_once 'application/Extras.php';
	
	// Debugging status
	if (DEBUG) 
	{
		// Report all errors, warnings, interoperability and compatibility
		//error_reporting(E_ALL|E_STRICT);
		// Show errors with output
		//ini_set("display_errors", "on");
		error_reporting(0);
		//ini_set("display_errors", "off");
	}
	else 
	{
		error_reporting(0);
		ini_set("display_errors", "off");
	}

	require_once '../application/DB_Connect.php';
    require_once '../models/Agent.php';
    require_once '../controllers/ControllerAgent.php';
    require_once '../controllers/ControllerBid.php';
    require_once '../controllers/ControllerAuction.php';
    require_once '../models/User.php';
    require_once '../models/Bid.php';
    require_once '../controllers/ControllerUser.php';
    require_once '../models/RealEstate.php';
    require_once '../controllers/ControllerRealEstate.php';
    require_once '../models/Photo.php';    
    require_once '../controllers/ControllerPhoto.php';
    require_once '../controllers/ControllerRest.php';
    require_once '../controllers/ControllerReservedProperty.php';
    require_once '../models/ReservedProperty.php';
    require_once '../controllers/ControllerFavoriteProperty.php';    
    require_once '../models/FavoriteProperty.php';
    require_once '../controllers/ControllerContact.php';
    require_once '../models/Contact.php';
    require_once '../models/Auction.php';
?>