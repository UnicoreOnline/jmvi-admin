<?php

    require_once '../header_rest.php';    
    require_once "../PHPMailer/PHPMailerAutoload.php";
    $extras = new Extras();
    $userController = new ControllerUser();
    $propertyController = new ControllerRealEstate();
    $controller = new ControllerReservedProperty();
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
		$isAllowed = 1;
		if ($property->status == 3) {
			$isAllowed = $controller->checkAccessForBid($property_id,$user_id);
        }
        if ($isAllowed == 0) {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'You are not allowed to bid on this property.');
            echo json_encode($arrayJSON);
            return;
        } else if ($isAllowed == 2) {
            $arrayJSON = array();
            $arrayJSON['status'] = array('status_code' => "5", 'status_text' => 'You have already registered to bid for this property. Please wait on a rep to contact you so as to move forward. Please check your inbox or spam for an email from us.');
            echo json_encode($arrayJSON);
            return;
        }
        
        $senderName = "JMVI RealEstate";
        $to = "jmviapp@gmail.com";

        //send the mail
        $userName = $user->full_name;
        $address = $property->address ? $property->address : null;
        $mobile = $user->mobile ? $user->mobile : 0;
        $email = $user->email;
        $propertyName = $property->pname;
        $propertyStatus = $property->status;

        $itm = new ReservedProperty();
        $itm->user_id = trim($user->user_id);
        $itm->user_name = trim($userName);
        $itm->user_email = trim($email);
        $itm->user_address = trim($address);
        $itm->mobile = trim($mobile);
        $itm->property_id = trim($property->realestate_id);
        $itm->property_name = trim($propertyName);
        $itm->property_status = trim($propertyStatus);
        $itm->created_at = date('Y-m-d H:i:s');
        $itm->is_allowed = 0;
        $resPropResult = $controller->insertReservedProperty($itm);
        
        
        if(!empty($property) && !empty($property->agent_id)) {

            $agentDetail = $agent->getAgentByAgentId($property->agent_id);

            if(!empty($agentDetail) && !empty($email)) {

                // Auction type
                if ($property->status == 3) {

                    // start to sending email
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = $to;
                    $mail->Password = "investmentjmvi";
                    $mail->SMTPSecure = "ssl";
                    $mail->Port = 465;
                    $mail->From = $to;
                    $mail->FromName = $senderName;
                    $mail->addAddress(trim($email), $userName);
                    $mail->addAttachment("../upload_pic/JMVI_Auction_Rules_&_Registration.pdf");
                    //$mail->AddCC('investmentjmvi@gmail.com', $senderName);
                    $mail->isHTML(true);
                    $mail->Subject = "Registered Bidder - Auction";

                    $mail->Body = "You have successfully registered to bid for {$propertyName} at ".date('H:i',strtotime($itm->created_at))." on ".date('Y-m-d',strtotime($itm->created_at))."</br><br/><br/>
                    Please review the attached auction pdf document and follow the instructions in order to move forward. 
                    </br><br/><br/>
                    Thank you. ";

                    try{
                        if (!$mail->send()) {
                            echo "Mailer Error: " . $mail->ErrorInfo;       
                            die;
                        }else {
                            // no need to update status once property reserved
                            //$propertyController->updateRealEstateStatus($itm->property_id,2);
                            //echo 'mail sent';                        
                        }

                    }catch(Exception $e) {
                        echo "Mailer Error: " . $e->getMessage();
                        //die;
                    } 

                    //Send to admin
                    $smail = new PHPMailer(true);
                    $smail->isSMTP();
                    $smail->Host = "smtp.gmail.com";
                    $smail->SMTPAuth = true;
                    $smail->Username = $to;
                    $smail->Password = "investmentjmvi";
                    $smail->SMTPSecure = "ssl";
                    $smail->Port = 465;
                    $smail->From = $to;
                    $smail->FromName = $senderName;
                    $smail->addAddress(trim("investmentjmvi@qq.com"), "");
                    $smail->addAddress(trim("investmentjmvi@gmail.com"), "");
                    //$mail->AddCC('investmentjmvi@gmail.com', $senderName);
                    $smail->isHTML(true);
                    $smail->Subject = "Registered Bidder - Auction";

                    $smail->Body = "{$userName} has registered to bid for {$propertyName} at ".date('H:i',strtotime($itm->created_at))." on ".date('Y-m-d',strtotime($itm->created_at))."</br><br/><br/>
                    Please contact {$userName} at {$email} or {$mobile} within the hour to finalize arrangement on property.
                    </br><br/><br/>
                    Thank you.";
                    try{
                        if (!$smail->send()) {
                            echo "Mailer Error: " . $smail->ErrorInfo;       
                            die;
                        }else {
                            // no need to update status once property reserved
                            //$propertyController->updateRealEstateStatus($itm->property_id,2);
                            //echo 'mail sent';                        
                        }

                    }catch(Exception $e) {
                        echo "Mailer Error: " . $e->getMessage();
                        //die;
                    } 

                } else {
                    //Send to admin
                    $smail = new PHPMailer(true);
                    $smail->isSMTP();
                    $smail->Host = "smtp.gmail.com";
                    $smail->SMTPAuth = true;
                    $smail->Username = $to;
                    $smail->Password = "investmentjmvi";
                    $smail->SMTPSecure = "ssl";
                    $smail->Port = 465;
                    $smail->From = $to;
                    $smail->FromName = $senderName;
                    $smail->addAddress(trim("investmentjmvi@gmail.com"), "");
                    $smail->addAddress(trim("investmentjmvi@qq.com"), "");
                    $smail->isHTML(true);
                    $smail->Subject = ($property->status == 1) ? "Property for sale on JMVI" : "Property for rent on JMVI";

                    $smail->Body = "Please contact {$userName} at {$mobile} or {$email} to arrange viewing of property.";
                    try{
                        if (!$smail->send()) {
                            echo "Mailer Error: " . $smail->ErrorInfo;       
                            die;
                        }else {
                            // no need to update status once property reserved
                            //$propertyController->updateRealEstateStatus($itm->property_id,2);
                            //echo 'mail sent';                        
                        }

                    }catch(Exception $e) {
                        echo "Mailer Error: " . $e->getMessage();
                        //die;
                    } 
                }
                
            }
        }   
        

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