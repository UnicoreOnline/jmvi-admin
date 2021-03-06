<?php

    require_once '../header_rest.php';
    $controllerRealEstate = new ControllerRealEstate();
    $controllerUser = new ControllerUser();
    $controllerPhoto = new ControllerPhoto();
    $controllerRest = new ControllerRest();

    $login_hash = "";
    if( !empty($_POST['login_hash']) )
        $login_hash = $_POST['login_hash'];

    $user_id = "";
    if( !empty($_POST['user_id']) )
        $user_id = $_POST['user_id'];

    $address = "";
    if( !empty($_POST['address']) )
        $address = $_POST['address'];

    $agent_id = 0;
    if( !empty($_POST['agent_id']) )
        $agent_id = $_POST['agent_id'];

    $baths = "";
    if( !empty($_POST['baths']) )
        $baths = $_POST['baths'];

    $beds = "";
    if( !empty($_POST['beds']) )
        $beds = trim(strip_tags($_POST['beds']));

    $built_in = 0;
    if( !empty($_POST['built_in']) )
        $built_in = trim(strip_tags($_POST['built_in']));

    $country = "";
    if( !empty($_POST['country']) )
        $country = $_POST['country'];

    $desc1 = "";
    if( !empty($_POST['desc1']) )
        $desc1 = trim(strip_tags($_POST['desc1']));

    $featured = "";
    if( !empty($_POST['featured']) )
        $featured = trim(strip_tags($_POST['featured']));

    $lat = "";
    if( !empty($_POST['lat']) )
        $lat = trim(strip_tags($_POST['lat']));

    $lon = "";
    if( !empty($_POST['lon']) )
        $lon = trim(strip_tags($_POST['lon']));

    $lot_size = "";
    if( !empty($_POST['lot_size']) )
        $lot_size = trim(strip_tags($_POST['lot_size']));

    $price = "";
    if( !empty($_POST['price']) )
        $price = $_POST['price'];

    $price_per_sqft = "";
    if( !empty($_POST['price_per_sqft']) )
      $price_per_sqft = trim(strip_tags($_POST['price_per_sqft']));

    $property_type = "";
    if( !empty($_POST['property_type']) )
        $property_type = trim(strip_tags($_POST['property_type']));

    $realestate_id = 0;
    if( !empty($_POST['realestate_id']) )
        $realestate_id = trim(strip_tags($_POST['realestate_id']));

    $rooms = "";
    if( !empty($_POST['rooms']) )
        $rooms = trim(strip_tags($_POST['rooms']));

    $sqft = "";
    if( !empty($_POST['sqft']) )
        $sqft = trim(strip_tags($_POST['sqft']));

    $status = "";
    if( !empty($_POST['status']) )
        $status = trim(strip_tags($_POST['status']));

    $zipcode = "";
    if( !empty($_POST['zipcode']) )
        $zipcode = trim(strip_tags($_POST['zipcode']));

    $currency = "";
    if( !empty($_POST['currency']) )
        $currency = trim(strip_tags($_POST['currency']));

    $is_deleted = 0;
    if( !empty($_POST['is_deleted']) )
        $is_deleted = $_POST['is_deleted'];

    $api_key = "";
    if( !empty($_POST['api_key']) )
        $api_key = $_POST['api_key'];

    $photo_ids_deleted = "";
    if( !empty($_POST['photo_ids_deleted']) )
        $photo_ids_deleted = $_POST['photo_ids_deleted'];

    $propertytype_id = 0;
    if( !empty($_POST['propertytype_id']) )
        $propertytype_id = trim(strip_tags($_POST['propertytype_id']));


    $max_photos_uploaded = 0;
    if(!empty($_POST['max_photos_uploaded']))
        $max_photos_uploaded = $_POST['max_photos_uploaded'];

    $arrayPhotos = array();
    $ind = 0;
    for($x = 0; $x < $max_photos_uploaded; $x++) {
        $key = "uploaded_file_realestate_" . $x;
        $file = $_FILES[$key]["name"];
        if(!empty($file) && count($file)) {
            $photo_url = getPhoto('realestate_', $key);
            $arrayPhotos[$ind] = $photo_url;
            $ind += 1;
        }
    }

    if( Constants::API_KEY != $api_key ) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "4", 'status_text' => 'Invalid API Access Key.');
        echo json_encode($arrayJSON);
        return;
    }

    if(empty($login_hash) && $user_id == 0) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'Invalid Access. User is missing login hash. Please relogin in the app.');
        echo json_encode($arrayJSON);
        return;
    }

    $is_valid = $controllerUser->isUserValid($user_id, $login_hash);
    if(!$is_valid) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "6", 'status_text' => 'Invalid Access. Invalid login hash. Please relogin in the app.');
        echo json_encode($arrayJSON);
        return;   
    }

    if($is_deleted == 1 && $realestate_id > 0) {
        $user = $controllerUser->getUserByUserId($user_id);
        $login_hash = str_replace(" ", "+", $login_hash);
        if($user != null) {
            if($user->login_hash == $login_hash) {
                $controllerRealEstate->deleteRealEstate($realestate_id, 1);
                $arrayJSON = array();
                $arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Success.');
                $arrayJSON['realestate_info'] = array('realestate_id' => $realestate_id, 'is_deleted' => 1);
                array_walk_recursive($arrayJSON, "convert_to_string");
                echo json_encode($arrayJSON);
                return; 
            }
            else {
                $arrayJSON = array();
                $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you are out of sync. Please relogin again.');
                echo json_encode($arrayJSON);
                return; 
            }
        }
    }
    
    else if( $realestate_id >= 0 && $agent_id > 0 ) {
        $user = $controllerUser->getUserByUserId($user_id);
        if($user == null) {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you are out of sync. Please relogin again.');
            echo json_encode($arrayJSON);
            return; 
        }
        if($user->login_hash != $login_hash) {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'It seems you are out of sync. Please relogin again.');
            echo json_encode($arrayJSON);
            return; 
        }

        $login_hash = str_replace(" ", "+", $login_hash);
        $realestate = $controllerRealEstate->getRealEstateByRealEstateId($realestate_id);
        
        $itm = new RealEstate();
        $itm->address = $address;
        $itm->agent_id = $agent_id;
        $itm->baths = $baths;
        $itm->beds = $beds;
        $itm->built_in = $built_in;
        $itm->country = $country;
        $itm->created_at = time();
        $itm->desc1 = $desc1;
        $itm->featured = $featured;
        $itm->lat = $lat;
        $itm->lon = $lon;
        $itm->lot_size = $lot_size;
        $itm->price = $price;
        $itm->price_per_sqft = $price_per_sqft;
        $itm->property_type = $property_type;
        $itm->realestate_id = $realestate_id;
        $itm->rooms = $rooms;
        $itm->sqft = $sqft;
        $itm->status = $status;
        $itm->updated_at = time();
        $itm->zipcode = $zipcode;
        $itm->currency = $currency;

        if($realestate != null) {
            $itm->created_at = $realestate->created_at;
            $itm->realestate_id = $realestate->realestate_id;
            $controllerRealEstate->updateRealEstate($itm);
        }
        else {
            $controllerRealEstate->insertRealEstate($itm);
            $realestate_id = $controllerRealEstate->getLastInsertedId();
        }

        if(strlen($photo_ids_deleted) > 0) {
            $explode = explode("~", $photo_ids_deleted);
            for($x = 0; $x < count($explode); $x++) {
                $photo_id = $explode[$x];
                $controllerPhoto->deletePhoto($photo_id, 1);
            }
        }

        $max_photos = count($arrayPhotos);
        if($max_photos > 0) {
            for($x = 0; $x < $max_photos; $x++) {
                $photo_url = $arrayPhotos[$x];

                $photo = new Photo();
                $photo->photo_url = $photo_url;
                $photo->thumb_url = $photo_url;
                $photo->updated_at = time();
                $photo->created_at = time();
                $photo->realestate_id    = $realestate_id;
                $controllerPhoto->insertPhoto($photo);
            }
        }

        $results = $controllerRest->getResultRealEstateByRealEstateId($realestate_id);
        $objRealEstate = getObj($results);

        $resultPhotos = $controllerRest->getPhotosResultByRealEstateId($realestate_id);
        $photosObj = getArrayObjs($resultPhotos);       

        $resultsPropertyType = $controllerRest->getPropertyTypeResultByPropertyTypeId($objRealEstate['property_type']);
        $property_type = getObj($resultsPropertyType);

        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Success.');
        $arrayJSON['realestate_info'] = $objRealEstate;
        $arrayJSON['photos'] = $photosObj;
        $arrayJSON['property_type_obj'] = $property_type;
        array_walk_recursive($arrayJSON, "convert_to_string");
        echo json_encode($arrayJSON);        
    }
    else {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "3", 'status_text' => 'Invalid Access.');
        echo json_encode($arrayJSON);
    }

    function getArrayObjs($results) {
        $ind = 0;
        $arrayObjs = array();
        foreach ($results as $row) {
            $arrayObj = array();
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    $arrayObj[$columnName] = $field;
                }
            }
            $arrayObjs[$ind] = $arrayObj;
            $ind += 1;
        }
        return $arrayObjs;
    }

    function getObj($results) {
        $arrayObj = array();
        foreach ($results as $row) {
            foreach ($row as $columnName => $field) {
                if(!is_numeric($columnName)) {
                    $arrayObj[$columnName] = $field;
                }
            }
            break;
        }
        return $arrayObj;
    }

    function getPhoto($file_prefix_name, $obj_name) {
        $file_path = "../".Constants::IMAGE_UPLOAD_DIR."/";
        $file_name = $_FILES[$obj_name]['name'];
        $split = explode(".", $file_name);
        $ext = end( $split );

        $new_file_name = $file_prefix_name . basename(uniqid()) . "." . $ext;
        $file_path = $file_path . $new_file_name;
        $photo_path = "../".Constants::IMAGE_UPLOAD_DIR."/";
        if(move_uploaded_file($_FILES[$obj_name]['tmp_name'], $file_path)) {
            return Constants::ROOT_URL."".Constants::IMAGE_UPLOAD_DIR."/".$new_file_name;
        }
        return "";
    }
?>