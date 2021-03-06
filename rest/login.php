<?php
    require_once '../header_rest.php';
    $controllerUser = new ControllerUser();
    $username = "";
	//file_put_contents("log.txt",json_encode($_POST));
    if( !empty($_REQUEST['username']) )
        $username = $_REQUEST['username'];

    $password = "";
    if( !empty($_REQUEST['password']) )
        $password = md5($_REQUEST['password']);

    $facebook_id = "";
    if( !empty($_REQUEST['facebook_id']) )
        $facebook_id = $_REQUEST['facebook_id'];

    $twitter_id = "";
    if( !empty($_REQUEST['twitter_id']) )
        $twitter_id = $_REQUEST['twitter_id'];

    $api_key = "";
    if( !empty($_REQUEST['api_key']) )
        $api_key = $_REQUEST['api_key'];
	
	$email = "";
    if( !empty($_REQUEST['email']) )
        $email = $_REQUEST['email'];

	$full_name = "";
    if( !empty($_REQUEST['full_name']) )
        $full_name = $_REQUEST['full_name'];	
	
    $apple_id = 0;
    if (!empty($_POST['apple_id']))
        $apple_id = $_POST['apple_id'];
    
    if(isset($_POST['api_key']) && !empty($_POST['api_key'])){
        $api_key = $_POST['api_key'];
        $twitter_id = $_POST['twitter_id'];
        $facebook_id = $_POST['facebook_id'];
        $password = md5($_POST['password']);
        $username = $_POST['username'];
    }

    if( Constants::API_KEY != $api_key ) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "4", 'status_text' => 'Invalid API Access Key.');
        array_walk_recursive($arrayJSON, "convert_to_string");
        echo json_encode($arrayJSON);
        return;
    }

    if( !empty($username) && !empty($password) ) { 
        $user = $controllerUser->loginUser($username, $password);
        if($user != null) {
            $controllerUser->updateUserHash($user);
            $arrayJSON = fetchArrayJSON($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
        else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "1", 'status_text' => 'Username/Password Invalid or you are being denied to access. Please try again.');
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
    }

    else if( !empty($facebook_id) ) {
        $user = $controllerUser->loginFacebook($facebook_id);
        if($user != null) {
            $controllerUser->updateUserHash($user);
            $arrayJSON = fetchArrayJSON($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
        else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "2", 'status_text' => 'Invalid Login.');
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
    }

    else if( !empty($twitter_id) ) {
        $user = $controllerUser->loginTwitter($twitter_id);
        if($user != null) {
            $controllerUser->updateUserHash($user);
            $arrayJSON = fetchArrayJSON($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
        else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "2", 'status_text' => 'Invalid Login.');
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
    }
    else if( !empty($apple_id) ) {
        $user = $controllerUser->loginApple($apple_id);
        if($user != null) {
			$isUpdate = false;
			if(!empty($email)) {
				$isUpdate = true;
				$user->email = $email;
				// update the hash
			}
			if(!empty($full_name)) {
				$isUpdate = true;
				$user->full_name = $full_name;
			}
			
			if($isUpdate) {
			   $controllerUser->updateUser($user);
			}
            $controllerUser->updateUserHash($user);
            $arrayJSON = fetchArrayJSON($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
        else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "2", 'status_text' => 'Invalid Login.');
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
    }
    else {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => "3", 'status_text' => 'Invalid Access.');
        array_walk_recursive($arrayJSON, "convert_to_string");
        echo json_encode($arrayJSON);
    }

    function fetchArrayJSON($itm) {
        $controllerAgent = new ControllerAgent();
        $agent = $controllerAgent->getAgentByUserId($itm->user_id);

        $arrayJSON = array();
        $arrayJSON['agent_info'] = null;

        if($agent != null) {
            $arrayJSON['agent_info'] = array('address' => $agent->address, 
                                            'agent_id' => $agent->agent_id, 
                                            'contact_no' => $agent->contact_no, 
                                            'country' => $agent->country,
                                            'created_at' => $agent->created_at,
                                            'email' => $agent->email,
                                            'name' => $agent->name,
                                            'sms' => $agent->sms,
                                            'updated_at' => $agent->updated_at,
                                            'zipcode' => $agent->zipcode,
                                            'photo_url' => $agent->photo_url,
                                            'thumb_url' => $agent->thumb_url,
                                            'twitter' => $agent->twitter,
                                            'fb' => $agent->fb,
                                            'linkedin' => $agent->linkedin,
                                            'company' => $agent->company,
                                            'user_id' => $itm->user_id);
        }

        $arrayJSON['user_info'] = array('user_id' => $itm->user_id, 
                                        'username' => $itm->username, 
                                        'login_hash' => $itm->login_hash, 
                                        'facebook_id' => $itm->facebook_id, 
                                        'twitter_id' => $itm->twitter_id, 
                                        'full_name' => $itm->full_name,
                                        'mobile' => $itm->mobile,
                                        'address' => $itm->address,
                                        'email' => $itm->email,
                                        'country' => $itm->country);

        $arrayJSON['status'] = array('status_code' => "-1", 
                                      'status_text' => 'Success.' );
        
        return $arrayJSON;
    }

?>