<?php

require_once '../header_rest.php';

$controllerUser = new ControllerUser();

$userId = "";
if (!empty($_POST['user_id']))
    $userId = $_POST['user_id'];

$username = "";
if (!empty($_POST['username']))
    $username = $_POST['username'];

$full_name = "";
if (!empty($_POST['full_name']))
    $full_name = $_POST['full_name'];

$email = "";
if (!empty($_POST['email']))
    $email = $_POST['email'];

$mobile = "";
if (!empty($_POST['mobile']))
    $mobile = $_POST['mobile'];

$address = "";
if (!empty($_POST['address']))
    $address = $_POST['address'];

$country = "";
if (!empty($_POST['country']))
    $country = $_POST['country'];

$api_key = "";
if (!empty($_POST['api_key']))
    $api_key = $_POST['api_key'];

if (Constants::API_KEY != $api_key) {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => '4', 'status_text' => 'Invalid API Access Key.');
    echo json_encode($arrayJSON);
    return;
}

if (!empty($userId)) {

    $itm = $controllerUser->getUserByUserId($userId);
    if (empty($itm)) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '5', 'status_text' => 'User not exists.');
        echo json_encode($arrayJSON);
        return;
    }

    if (!empty($username) && $username != $itm->username) {
        $isUserExist = $controllerUser->isUserExist($username);
        if ($isUserExist) {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '2', 'status_text' => 'Username Exist.');
            echo json_encode($arrayJSON);
            return;
        }
    }

    if (!empty($email) && $email != $itm->email) {
        $isEmailExist = $controllerUser->isEmailExist($email);
        if ($isEmailExist) {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '1', 'status_text' => 'Email already registered.');
            echo json_encode($arrayJSON);
            return;
        }
    }

    $itm->username = !empty($username) ? $username : $itm->username;
    $itm->full_name = $full_name;
    $itm->email = !empty($email) ? $email : $itm->email;
    $itm->mobile = $mobile;
    $itm->address = $address;
    $itm->country = $country;

    $controllerUser->updateProfile($itm);

    $arrayJSON = getUserData($itm);
    array_walk_recursive($arrayJSON, "convert_to_string");
    echo json_encode($arrayJSON);
} else {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Please send user id.');
    echo json_encode($arrayJSON);
}


function getUserData($itm)
{
    $controllerAgent = new ControllerAgent();
    $agent = $controllerAgent->getAgentByUserId($itm->user_id);

    $arrayJSON = array();
    $arrayJSON['agent_info'] = null;


    $arrayUser = array('user_id' => $itm->user_id,
        'username' => $itm->username,
        'login_hash' => $itm->login_hash,
        'facebook_id' => $itm->facebook_id,
        'twitter_id' => $itm->twitter_id,
        'full_name' => $itm->full_name,
        'mobile' => $itm->mobile,
        'address' => $itm->address,
        'country' => $itm->country,
        'email' => $itm->email);

    $arrayJSON['user_info'] = $arrayUser;
    $arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'You have successfully updated profile.');
     
    if ($agent != null) {
        $arrayAgent = array('address' => $agent->address,
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
            'company' => $agent->company);

        $arrayJSON['agent_info'] = $arrayAgent;
    }

    return $arrayJSON;
}

?>