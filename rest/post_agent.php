<?php

    require_once '../header_rest.php';
    
    $controllerRestAgent = new ControllerAgent();
    $controllerUser = new ControllerUser();

    $password ="";
    if( !empty($_POST['password']) )
        $password = md5($_POST['password']);

    $user_id ="";
    if( !empty($_POST['user_id']) )
        $user_id = $_POST['user_id'];

    $login_hash ="";
    if( !empty($_POST['login_hash']) )
        $login_hash = $_POST['login_hash'];

    $address ="";
    if( !empty($_POST['address']) )
        $address = trim(strip_tags($_POST['address']));

    $contact_no ="";
    if( !empty($_POST['contact_no']) )
        $contact_no = trim(strip_tags($_POST['contact_no']));

    $country ="";
    if( !empty($_POST['country']) )
        $country = trim(strip_tags($_POST['country']));

    $email ="";
    if( !empty($_POST['email']) )
        $email = $_POST['email'];

    $name ="";
    if( !empty($_POST['name']) )
        $name = $_POST['name'];

    $sms ="";
    if( !empty($_POST['sms']) )
        $sms = trim(strip_tags($_POST['sms']));

    $zipcode ="";
    if( !empty($_POST['zipcode']) )
        $zipcode = trim(strip_tags($_POST['zipcode']));

    $twitter ="";
    if( !empty($_POST['twitter']) )
        $twitter = trim(strip_tags($_POST['twitter']));

    $fb ="";
    if( !empty($_POST['fb']) )
        $fb = trim(strip_tags($_POST['fb']));

    $linkedin ="";
    if( !empty($_POST['linkedin']) )
        $linkedin = trim(strip_tags($_POST['linkedin']));

    $company ="";
    if( !empty($_POST['company']) )
        $company = trim(strip_tags($_POST['company']));

    $api_key = "";
    if(!empty($_POST['api_key']))
        $api_key = $_POST['api_key'];

    
    $thumb_url = "";
    if(!empty($_POST["thumb_url"])) {
        $thumb_url = $_POST['thumb_url'];
    }
    
    if(!empty($_FILES["uploaded_file_thumb"]["name"])) {
        $count = count($_FILES["uploaded_file_thumb"]["name"]);
        if($count > 0)
            $thumb_url = getPhoto('thumb_', 'uploaded_file_thumb');
    }
    
    $photo_url = "";
    if(!empty($_POST["photo_url"])) {
        $photo_url = $_POST['photo_url'];
    }
    
    if(!empty($_FILES["uploaded_file_photo"]["name"])) {
        $count = count($_FILES["uploaded_file_photo"]["name"]);
        if($count > 0)
            $photo_url = getPhoto('cover_', 'uploaded_file_photo');
    }


    if(Constants::API_KEY != $api_key) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access. API KEY is wrong.');
        echo json_encode($arrayJSON);
        return;
    }

    if(empty($name) || empty($contact_no) || empty($email) || empty($sms) || empty($address) || empty($login_hash) || empty($user_id)) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access.');
        echo json_encode($arrayJSON);
        return;
    }

    $user = $controllerUser->getUserByUserId($user_id);
    $login_hash = str_replace(" ", "+", $login_hash);   
    if($user == null) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '5', 'status_text' => 'It seems you are out of sync. Please relogin again.');
        echo json_encode($arrayJSON);
        return;
    }
    if($user->login_hash != $login_hash) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '5', 'status_text' => 'It seems you are out of sync. Please relogin again.');
        echo json_encode($arrayJSON);
        return;
    }
    if(!empty($password)) {
        $controllerUser->changePassword($user_id, $password);
    }


    $agent = $controllerRestAgent->getAgentByUserId($user_id);
    $itm = new Agent();
    $itm->address = $address;
    $itm->contact_no = $contact_no;
    $itm->country = $country;
    $itm->created_at = time();
    $itm->email = $email;
    $itm->name = $name;
    $itm->sms = $sms;
    $itm->updated_at = time();
    $itm->zipcode = $zipcode;
    $itm->photo_url = $photo_url;
    $itm->thumb_url = $thumb_url;
    $itm->twitter = $twitter;
    $itm->fb = $fb;
    $itm->linkedin = $linkedin;
    $itm->company = $company;
    $itm->user_id = $user_id;

    if($agent != null) {
        $itm->created_at = $agent->created_at;
        $itm->agent_id = $agent->agent_id;
        $controllerRestAgent->updateAgent($itm);
    }
    else {
        $controllerRestAgent->insertAgent($itm);
    }

    $itm = $controllerRestAgent->getAgentByUserId($user_id);
    
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'Success.');
    $arrayJSON['agent_info'] = array(
                                'address' => $itm->address, 
                                'agent_id' => $itm->agent_id, 
                                'contact_no' => $itm->contact_no,
                                'country' => $itm->country,
                                'created_at' => $itm->created_at,
                                'email' => $itm->email,
                                'name' => $itm->name,
                                'sms' => $itm->sms,
                                'updated_at' => $itm->updated_at,
                                'zipcode' => $itm->zipcode,
                                'photo_url' => $itm->photo_url,
                                'thumb_url' => $itm->thumb_url,
                                'twitter' => $itm->twitter, 
                                'fb' => $itm->fb, 
                                'linkedin' => $itm->linkedin, 
                                'company' => $itm->company, 
                                'user_id' => $user->user_id);

    $arrayJSON['user_info'] = array(
                                    'user_id' => $user->user_id,
                                    'username' => $user->username,
                                    'login_hash' => $user->login_hash,
                                    'facebook_id' => $user->facebook_id,
                                    'twitter_id' => $user->twitter_id,
                                    'full_name' => $user->full_name,
                                    'email' => $user->email);

    array_walk_recursive($arrayJSON, "convert_to_string");
    echo json_encode($arrayJSON);

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