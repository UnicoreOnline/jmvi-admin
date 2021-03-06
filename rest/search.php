<?php
require '../header_rest.php';
$controllerRest = new ControllerRest();

$api_key = "";
if (!empty($_POST['api_key'])) {
    $api_key = $_POST['api_key'];
}

$lat = 0;
if (!empty($_POST['lat'])) {
    $lat = str_replace(",", ".", $_POST['lat']);
}

$lon = 0;
if (!empty($_POST['lon'])) {
    $lon = str_replace(",", ".", $_POST['lon']);
}

$radius = 0;
if (!empty($_POST['radius'])) {
    $radius = $_POST['radius'];
}

$price_min = 0;
if (!empty($_POST['price_min'])) {
    $price_min = $_POST['price_min'];
}

$price_max = 0;
if (!empty($_POST['price_max'])) {
    $price_max = $_POST['price_max'];
}

$lot_size_min = 0;
if (!empty($_POST['lot_size_min'])) {
    $lot_size_min = $_POST['lot_size_min'];
}

$lot_size_max = 0;
if (!empty($_POST['lot_size_max'])) {
    $lot_size_max = $_POST['lot_size_max'];
}

$built_in_max = 0;
if (!empty($_POST['built_in_max'])) {
    $built_in_max = $_POST['built_in_max'];
}

$built_in_min = 0;
if (!empty($_POST['built_in_min'])) {
    $built_in_min = $_POST['built_in_min'];
}

$sqft_min = 0;
if (!empty($_POST['sqft_min'])) {
    $sqft_min = $_POST['sqft_min'];
}

$sqft_max = 0;
if (!empty($_POST['sqft_max'])) {
    $sqft_max = $_POST['sqft_max'];
}

$beds = 0;
if (!empty($_POST['beds'])) {
    $beds = $_POST['beds'];
}

$baths = 0;
if (!empty($_POST['baths'])) {
    $baths = $_POST['baths'];
}

$property_type = 0;
if (!empty($_POST['property_type'])) {
    $property_type = $_POST['property_type'];
}

$status = '';
if (isset($_POST['status'])) {
    $status = $_POST['status'];
}

$address = "";
if (!empty($_POST['address'])) {
    $address = trim($_POST['address']);
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

$params = array('price_min' => $price_min,
    'price_max'                 => $price_max,
    'lot_size_min'              => $lot_size_min,
    'lot_size_max'              => $lot_size_max,
    'built_in_min'              => $built_in_min,
    'built_in_max'              => $built_in_max,
    'sqft_min'                  => $sqft_min,
    'sqft_max'                  => $sqft_max,
    'baths'                     => $baths,
    'beds'                      => $beds,
    'property_type'             => $property_type,
    'lat'                       => $lat,
    'lon'                       => $lon,
    'radius'                    => $radius,
    'status'                    => $status,
    'address'                   => $address,
    'country'                   => $country
    );

$results = $controllerRest->searchRealEstateResult($params);

$arrayJSON                 = array();
$arrayJSON['result_count'] = $results->rowCount();
if($status == 3){
    $arrayJSON['real_estates'] = getArrayObjsAuction($results);
} else {
    $arrayJSON['real_estates'] = getArrayObjsRealEstate($results);
}
$arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'Success.');
array_walk_recursive($arrayJSON, "convert_to_string");
echo json_encode($arrayJSON);

function getArrayObjsRealEstate($results)
{
    $controllerRest = new ControllerRest();
    $ind            = 0;
    $arrayObjs      = array();
    foreach ($results as $row) {
        $arrayObj = array();
        foreach ($row as $columnName => $field) {
            if (!is_numeric($columnName)) {
                $arrayObj[$columnName] = $field;
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
       
        $arrayObjs[$ind]    = $arrayObj;
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
    $ind       = 0;
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
function getArrayObjsAuction($results)
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
        if (!empty($arrayObj['realestate_id'])) {
            $resultRealestate = $controllerRest->getResultRealEstateByRealEstateId($arrayObj['realestate_id']);
            $realestateObject = getArrayObjs($resultRealestate);
            $arrayObj['realestate'] = count($realestateObject) > 0 ? $realestateObject : null;
        }

        $photosObj = array();
        if (!empty($arrayObj['realestate_id'])) {
            $resultPhotos = $controllerRest->getPhotosResultByRealEstateId($arrayObj['realestate_id']);
            $photosObj = getArrayObjs($resultPhotos);
            $arrayObj['photos'] = count($photosObj) > 0 ? $photosObj : null;
        }

        $bidObj = array();
        if (!empty($arrayObj['auction_id'])) {
            $resultBids = $controllerRest->getBidByAuctionId($arrayObj['auction_id']);
            $bidObj = getArrayObjs($resultBids->fetchAll());
            $arrayObj['bids'] = count($bidObj) > 0 ? $bidObj : [];
        }

        // $arrayObj['property_type_obj'] = $property_type;
        $arrayObjs[$ind] = $arrayObj;
        $ind += 1;
    }
    return $arrayObjs;
}