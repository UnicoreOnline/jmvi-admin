<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../header_rest.php';
$controllerUser = new ControllerUser();

$username = "";
if (!empty($_POST['username']))
    $username = $_POST['username'];

$password = "";
if (!empty($_POST['password']))
    $password = md5($_POST['password']);

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

$facebook_id = 0;
if (!empty($_POST['facebook_id']))
    $facebook_id = $_POST['facebook_id'];

$twitter_id = 0;
if (!empty($_POST['twitter_id']))
    $twitter_id = $_POST['twitter_id'];

$api_key = "";
if (!empty($_POST['api_key']))
    $api_key = $_POST['api_key'];

$apple_id = 0;
if (!empty($_POST['apple_id']))
    $apple_id = $_POST['apple_id'];

if (Constants::API_KEY != $api_key) {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => '4', 'status_text' => 'Invalid API Access Key.');
    echo json_encode($arrayJSON);
    return;
}

// if( empty($username) && empty($password) && empty($full_name) && empty($email) ) {
// 	$arrayJSON = array();
//     $arrayJSON['status'] = array('status_code' => '4', 'status_text' => 'Invalid Access.');
//     echo json_encode($arrayJSON);
//     return;
// }

if (!empty($username) && !empty($password) && !empty($full_name) && !empty($email)) {

    $isUserExist = $controllerUser->isUserExist($username);
    if ($isUserExist) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '2', 'status_text' => 'Username Exist.');
        echo json_encode($arrayJSON);
        return;
    }

    $isEmailExist = $controllerUser->isEmailExist($email);
    if ($isEmailExist) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '1', 'status_text' => 'Email already registered.');
        echo json_encode($arrayJSON);
        return;
    }

    $itm = new User();
    $itm->username = $username;
    $itm->password = $password;
    $itm->full_name = $full_name;
    $itm->email = $email;
    $itm->mobile = $mobile;
    $itm->address = $address;
    $itm->country = $country;
    $itm->facebook_id = '';
    $itm->twitter_id = '';

    $controllerUser->registerUser($itm);
    $user = $controllerUser->loginUser($username, $itm->password);

    if ($user == null) {
        $arrayJSON = array();
        $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Username/Password Invalid.');
        echo json_encode($arrayJSON);
        return;
    }

    $controllerUser->updateUserHash($user);
    $arrayJSON = getUserData($user);
    array_walk_recursive($arrayJSON, "convert_to_string");
    echo json_encode($arrayJSON);
} else if ($facebook_id > 0) {
    if (!$controllerUser->isFacebookIdExist($facebook_id)) {
        $itm = new User();
        $itm->username = '';
        $itm->password = '';
        $itm->full_name = $full_name;
        $itm->email = $email;
        $itm->facebook_id = $facebook_id;
        $itm->twitter_id = '';
        $itm->apple_id = '';

        $user = $controllerUser->loginFacebook($facebook_id);
        if ($user == null)
            $controllerUser->registerUser($itm);

        $user = $controllerUser->loginFacebook($facebook_id);
        if ($user != null) {
            // update the hash
            $controllerUser->updateUserHash($user);
            $arrayJSON = getUserData($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        }
    } else {
        $user = $controllerUser->loginFacebook($facebook_id);
        if ($user != null) {
            // update the hash
            $controllerUser->updateUserHash($user);
            $arrayJSON = getUserData($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        } else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Username/Password Invalid.');
            echo json_encode($arrayJSON);
        }
    }
} else if ($twitter_id > 0) {
    if (!$controllerUser->isTwitterIdExist($twitter_id)) {
        $itm = new User();
        $itm->username = '';
        $itm->password = '';
        $itm->full_name = $full_name;
        $itm->email = $email;
        $itm->facebook_id = '';
        $itm->twitter_id = $twitter_id;
        $itm->apple_id = '';
        
        $controllerUser->registerUser($itm);
        $user = $controllerUser->loginTwitter($twitter_id);
        if ($user != null) {
            // update the hash
            $controllerUser->updateUserHash($user);
            $arrayJSON = getUserData($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        } else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Username/Password Invalid.');
            echo json_encode($arrayJSON);
        }
    } else {
        $user = $controllerUser->loginTwitter($twitter_id);
        if ($user != null) {
            // update the hash
            $controllerUser->updateUserHash($user);
            $arrayJSON = getUserData($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        } else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Username/Password Invalid.');
            echo json_encode($arrayJSON);
        }
    }
} else if ($apple_id > 0) {
    if (!$controllerUser->isAppleIdExist($apple_id)) {
        $itm = new User();
        $itm->username = '';
        $itm->password = '';
        $itm->full_name = $full_name;
        $itm->email = $email;
        $itm->facebook_id = '';
        $itm->twitter_id = '';
        $itm->apple_id = $apple_id;

        $controllerUser->registerUser($itm);
        $user = $controllerUser->loginApple($apple_id);
        if ($user != null) {
            // update the hash
            $controllerUser->updateUserHash($user);
            $arrayJSON = getUserData($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        } else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Username/Password Invalid.');
            echo json_encode($arrayJSON);
        }
    } else {
        $user = $controllerUser->loginApple($apple_id);
        if ($user != null) {
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
            $arrayJSON = getUserData($user);
            array_walk_recursive($arrayJSON, "convert_to_string");
            echo json_encode($arrayJSON);
        } else {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Username/Password Invalid.');
            echo json_encode($arrayJSON);
        }
    }
} else {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => '3', 'status_text' => 'Invalid Access.');
    echo json_encode($arrayJSON);
}


function getUserData($itm)
{
    $controllerAgent = new ControllerAgent();
    $agent = $controllerAgent->getAgentByUserId($itm->user_id);

    $arrayJSON = array();
    $arrayJSON['agent_info'] = null;


    $arrayUser = array(
        'user_id' => $itm->user_id, 
        'username' => $itm->username, 
        'login_hash' => $itm->login_hash, 
        'facebook_id' => $itm->facebook_id, 
        'twitter_id' => $itm->twitter_id, 
        'full_name' => $itm->full_name,
        'mobile' => $itm->mobile,
        'address' => $itm->address,
        'email' => $itm->email,
        'country' => $itm->country);

    $arrayJSON['user_info'] = $arrayUser;
    $arrayJSON['status'] = array('status_code' => '-1', 'status_text' => 'Success.');

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