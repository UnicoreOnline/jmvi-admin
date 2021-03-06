<?php
require '../header_rest.php';
$controllerRest = new ControllerRest();

$api_key = "";
if (!empty($_GET['api_key']))
    $api_key = $_GET['api_key'];

$lat = 0;
if (!empty($_GET['lat']))
    $lat = str_replace(",", ".", $_GET['lat']);

$lon = 0;
if (!empty($_GET['lon']))
    $lon = str_replace(",", ".", $_GET['lon']);

$radius = 0;
if (!empty($_GET['radius']))
    $radius = $_GET['radius'];

$agent_id = 0;
if (!empty($_GET['agent_id']))
    $agent_id = $_GET['agent_id'];

$propertytype_id = 0;
if (!empty($_GET['propertytype_id']))
    $propertytype_id = $_GET['propertytype_id'];

$latest_count = 0;
if (!empty($_GET['latest_count']))
    $latest_count = $_GET['latest_count'];

$home_fetch_with_radius = 0;
if (!empty($_GET['home_fetch_with_radius']))
    $home_fetch_with_radius = $_GET['home_fetch_with_radius'];

$featured = 0;
if (!empty($_GET['featured']))
    $featured = $_GET['featured'];

$get_propertytypes = 0;
if (!empty($_GET['get_propertytypes']))
    $get_propertytypes = $_GET['get_propertytypes'];

$default_count_to_find_distance = 10;
if (!empty($_GET['default_count_to_find_distance']))
    $default_count_to_find_distance = $_GET['default_count_to_find_distance'];

$for_rent = 0;
if (!empty($_GET['for_rent']))
    $for_rent = $_GET['for_rent'];

$for_sale = 0;
if (!empty($_GET['for_sale']))
    $for_sale = $_GET['for_sale'];

$get_agents = 0;
if (!empty($_GET['get_agents']))
    $get_agents = $_GET['get_agents'];

$get_auction = 0;
if (!empty($_GET['get_auction']))
    $get_auction = $_GET['get_auction'];

$get_bank = 0;
if (!empty($_GET['get_bank']))
    $get_bank = $_GET['get_bank'];

$get_lawyer = 0;
if (!empty($_GET['get_lawyer']))
    $get_lawyer = $_GET['get_lawyer'];

$favorite = 0;
if (!empty($_GET['favorite']))
    $favorite = $_GET['favorite'];

$get_country = 0;
if (!empty($_GET['get_country']))
    $get_country = $_GET['get_country'];

$user_id = 0;
if (!empty($_GET['user_id']))
    $user_id = $_GET['user_id'];

$country = "";
if (!empty($_GET['country'])) {
    $country = trim($_GET['country']);
}

$auction_id = 0;
if (!empty($_GET['auction_id'])){
    $auction_id = $_GET['auction_id'];
}

$property_id = 0;
if (!empty($_GET['property_id'])){
    $property_id = $_GET['property_id'];
}

$arrayJSON = array();
if (Constants::API_KEY != $api_key) {
    $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access.');
    array_walk_recursive($arrayJSON, "convert_to_string");
    echo json_encode($arrayJSON);
    return;
}

// if($lat == 0 || $lon == 0 || $radius <= 0) {
//     $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access.');
//     echo json_encode($arrayJSON);
//     return;
// }

$get_banner = 0;
if (!empty($_GET['get_banner'])){
    $get_banner = $_GET['get_banner'];
}

if($get_banner == 1){    
    $bannerParam = [];
    $results = $controllerRest->getBannerResult($bannerParam);    
    $arrayJSON['result_count'] = $results->rowCount();    
    $arrayJSON['banner'] = getObj($results);
}

if ($get_auction == 1) {
    $auctionParam = [
        'country' => $country,
        'user_id' => $user_id
    ];
    $results = $controllerRest->getAuctionResult($auctionParam);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['auction'] = getArrayObjsAuction($results,$country);
    
}

if ($get_agents == 1) {
    $results = $controllerRest->getAgentsResult($country);
    $arrayJSON['result_count'] = $results->rowCount();
    
    $ageParam = [
        'lat' =>$lat,
        'lon' =>$lon,
        'radius' =>$radius,
        'agent_id' =>$agent_id,
        'user_id' =>$user_id,
        'country' => $country
    ];
    $arrayJSON['agents'] = getArrayObjsAgent($results,$ageParam);
    
    //$arrayJSON['agents'] = getArrayObjs($results);
    
}

if ($get_propertytypes == 1) {
    $results = $controllerRest->getPropertyTypeResult();
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['property_types'] = getArrayObjs($results);
    
}
if ($auction_id > 0) {
    
    $seParams = [
        'auction_id' => $auction_id,
        'user_id' => $user_id
    ];
    $results = $controllerRest->getAuctionById($seParams);
    $arrayJSON['result_count'] = $results->rowCount();
    $auctionDetail = getArrayObjsAuction($results);
    $arrayJSON['auction'] = isset($auctionDetail[0]) ? $auctionDetail[0] : [];    
} else if ($property_id > 0) {
    $seParams = [
        'property_id' => $property_id,
        'lat' => $lat,
        'lon' => $lon,
        'lon' => $radius,
        'user_id' => $user_id
    ];    
    
    $results = $controllerRest->getPropertyById($seParams);
    $arrayJSON['result_count'] = $results->rowCount();
    $propertyDetail = getArrayObjsRealEstate($results);
    $arrayJSON['real_estates'] = isset($propertyDetail[0]) ? $propertyDetail[0] : [];    
} else if ($lat != 0 && $lon != 0 && $radius > 0 && $featured == 1) {
    $results = $controllerRest->getRealEstateResultRadiusFeatured($lat, $lon, $radius, $user_id, $country);    
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($lat != 0 && $lon != 0 && $radius > 0 && $for_rent == 1) {
    $results = $controllerRest->getRealEstateResultRadiusStatus($lat, $lon, $radius, 0, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($lat != 0 && $lon != 0 && $radius > 0 && $agent_id > 0) {
    $results = $controllerRest->getRealEstateResultRadiusAgentById($lat, $lon, $radius, $agent_id, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($lat != 0 && $lon != 0 && $radius > 0 && $for_sale == 1) {
    $results = $controllerRest->getRealEstateResultRadiusStatus($lat, $lon, $radius, 1, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($propertytype_id > 0 && $lat != 0 && $lon != 0 && $radius > 0) {
    $results = $controllerRest->getRealEstateResultRadiusByPropertyTypeId($lat, $lon, $radius, $propertytype_id, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($agent_id > 0 && $lat != 0 && $lon != 0 && $radius > 0) {
    $results = $controllerRest->getRealEstateResultRadiusByAgentId($lat, $lon, $radius, $agent_id, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($agent_id > 0 && $lat != 0 && $lon != 0 && $radius == 0) {
    $results = $controllerRest->getRealEstateResultByAgentId($lat, $lon, $agent_id, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
    
} else if ($lat != 0 && $lon != 0 && $radius > 0) {
    $results = $controllerRest->getRealEstateResultRadius($lat, $lon, $radius, $user_id, $country);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
} else if ($get_bank > 0) {
    $results = $controllerRest->getBanksResult();
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['bank'] = getArrayObjs($results);
} else if ($get_lawyer > 0) {
    $results = $controllerRest->getLawyerResult();
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['lawyer'] = getArrayObjs($results);
} else if ($favorite > 0) {
    $results = $controllerRest->getRealEstateResultFavorite($user_id);
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);  
} else if ($get_country > 0) {
    $results = $controllerRest->getCountryResult();
    $arrayJSON['result_count'] = $results->rowCount();
    $arrayJSON['country'] = getArrayObjs($results);     
}


$max_distance = $controllerRest->getMaxDistanceFound($lat, $lon);
$default_distance = $controllerRest->getMaxDistanceFoundDefaultToCount($lat, $lon, $default_count_to_find_distance);
$arrayJSON['max_distance'] = $max_distance;
$arrayJSON['default_distance'] = $default_distance;

$arrayJSON['status'] = array('status_code' => -1, 'status_text' => 'Success.');
array_walk_recursive($arrayJSON, "convert_to_string");
$arrayJSON  = empty($arrayJSON) ? [] : $arrayJSON;
echo json_encode($arrayJSON);


function getArrayObjsRealEstate($results)
{
    $controllerRest = new ControllerRest();
    $ind = 0;
    $arrayObjs = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
                if($columnName == 'pdes'){
                    $arrayObj[$columnName] = str_replace("\r\n","\n",$field); 
                }
            }
        }

        $agentsObj = array();
        if (!empty($arrayObj['agent_id'])) {
            $resultAgent = $controllerRest->getAgentsResultByAgentId($arrayObj['agent_id']);
            $agentsObj = getObj($resultAgent);
            $arrayObj['agent'] = count($agentsObj) > 0 ? $agentsObj : null;
        }


        $photosObj = array();
        if (!empty($arrayObj['realestate_id'])) {
            $resultPhotos = $controllerRest->getPhotosResultByRealEstateId($arrayObj['realestate_id']);
            $photosObj = getArrayObjs($resultPhotos);
            $arrayObj['photos'] = count($photosObj) > 0 ? $photosObj : null;
        }

        $property_type = array();
        if (!empty($arrayObj['property_type'])) {
            $resultsPropertyType = $controllerRest->getPropertyTypeResultByPropertyTypeId($arrayObj['property_type']);
            $property_type = getObj($resultsPropertyType);
        }
        // $arrayObj['property_type_obj'] = $property_type;
        $arrayObjs[$ind] = $arrayObj;
        $ind += 1;
    }
    return $arrayObjs;
}

function getArrayObjsAuction($results, $countryStr = '')
{
    $controllerRest = new ControllerRest();
    $ind = 0;
    $arrayObjs = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
            }
        }

        $realestateObject = array();
        if (!empty($arrayObj['property_id'])) {
            $resultRealestate = $controllerRest->getResultRealEstateByRealEstateId($arrayObj['property_id'], $countryStr);
            $realestateObject = getArrayObjs($resultRealestate);
            $arrayObj['realestate'] = count($realestateObject) > 0 ? $realestateObject : null;
        }

        $photosObj = array();
        if (!empty($arrayObj['property_id'])) {
            $resultPhotos = $controllerRest->getPhotosResultByRealEstateId($arrayObj['property_id']);
            $photosObj = getArrayObjs($resultPhotos);
            $arrayObj['photos'] = count($photosObj) > 0 ? $photosObj : null;
        }

        $bidObj = array();
        if (!empty($arrayObj['id'])) {
            $resultBids = $controllerRest->getBidByAuctionId($arrayObj['id']);
            $bidObj = getArrayObjs($resultBids->fetchAll());
            $arrayObj['bids'] = count($bidObj) > 0 ? $bidObj : [];
        }

        // $arrayObj['property_type_obj'] = $property_type;
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

function getArrayObjs($results)
{
    $ind = 0;
    $arrayObjs = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
                if($columnName == 'pdes'){
                    $arrayObj[$columnName] = str_replace("\r\n","\n",$field); 
                }
            }
        }
        $arrayObjs[$ind] = $arrayObj;
        $ind += 1;
    }
    return $arrayObjs;
}

function getArrayObjsAgent($results, $params)
{
    $controllerRest = new ControllerRest();
    $ind = 0;
    $arrayObjs = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
            }
        }
        
        if (!empty($arrayObj['agent_id'])) {            
            //$lat, $lon, $radius, $agent_id, $user_id = 0, $country = ''
            
            $results = $controllerRest->getRealEstateResultAgentById($params['lat'], $params['lon'], $params['radius'], $arrayObj['agent_id'], $params['user_id'], $params['country']);            
            $arrayObj['realestate'] = getArrayObjsRealEstate($results);
        }

        // $arrayObj['property_type_obj'] = $property_type;
        $arrayObjs[$ind] = $arrayObj;
        $ind += 1;
    }
    return $arrayObjs;
}

?>