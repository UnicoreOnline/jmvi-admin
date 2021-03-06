<?php

    require_once '../header_rest.php';    
    require_once "../PHPMailer/PHPMailerAutoload.php";
    $extras = new Extras();
    $userController = new ControllerUser();
    $propertyController = new ControllerRealEstate();
    $controller = new ControllerFavoriteProperty();
    $agent = new ControllerAgent();
    

    $login_hash = "";
    if( !empty($_POST['login_hash']) )
        $login_hash = $_POST['login_hash'];

    $user_id = "";
    if( !empty($_POST['user_id']) )
        $user_id = $_POST['user_id'];

    $property_id = "";
    if( !empty($_POST['property_id']) )
        $property_id = $_POST['property_id'];

    $api_key = "";
    if( !empty($_POST['api_key']) )
        $api_key = $_POST['api_key'];

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

    $is_valid = $userController->isUserValid($user_id, $login_hash);
    if(!$is_valid) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "6", 'status_text' => 'Invalid Access. Invalid login hash. Please relogin in the app.');
        echo json_encode($arrayJSON);
        return;   
    }
    
    if(empty($property_id)) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "7", 'status_text' => 'Invalid Request. User is missing Property ID.');
        echo json_encode($arrayJSON);
        return;
    }

    
    
    if( $property_id > 0 ) {
        $user = $userController->getUserByUserId($user_id);
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

        
        $property = $propertyController->getRealEstateByRealEstateId($property_id);
        

        $itm = new FavoriteProperty();
        $itm->user_id = trim($user->user_id);        
        $itm->property_id = trim($property->realestate_id);        
        $itm->created_at = date('Y-m-d H:i:s');
        $resPropResult = $controller->insertFavoriteProperty($itm);        

        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Success.');
        $arrayJSON['data'] = $resPropResult;        
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
?>