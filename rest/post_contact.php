<?php

require_once '../header_rest.php';
require_once "../PHPMailer/PHPMailerAutoload.php";
$extras = new Extras();
$contactController = new ControllerContact();

$api_key = "";
if (!empty($_POST['api_key']))
    $api_key = trim($_POST['api_key']);

if (Constants::API_KEY != $api_key) {
    $arrayJSON = array();
    $arrayJSON['status'] = array('status_code' => "4", 'status_text' => 'Invalid API Access Key.');
    echo json_encode($arrayJSON);
    return;
}

$senderName = "JMVI RealEstate";
$to = "jmviapp@gmail.com";

//send the mail
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] :'';
$subject = isset($_POST['subject']) ? $_POST['subject'] :'';
$message = isset($_POST['message']) ? $_POST['message'] :'';


$itm = new Contact();
$itm->name = trim($name);
$itm->email = trim($email);
$itm->subject = trim($subject);
$itm->message = trim($message);

$contactResult = $contactController->submitContact($itm);

if (!empty($email)) {
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
    $mail->FromName = $name;
    $mail->addAddress('investmentjmvi@gmail.com', $name);
    //$mail->addAddress('pinaltkothiya@gmail.com', $name);
    $mail->addReplyTo($email, $name);
    //$mail->AddCC('investmentjmvi@gmail.com', $senderName);
    $mail->isHTML(true);
    $mail->Subject = "Reservation - JMVI";
    $mail->Body = "<p>Hi,</p>
                <p>Below is the contact detail:</p>
                <p>Name: ".$name."</p>
                <p>Email: ".$email."</p>
                <p>Subject: ".$subject."</p>
                <p>Message: ".$message."</p>
                <p>Thank you<br/>JMVI</p>";
    try {
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            die;
        } else {
            // no need to update status once property reserved
            //$propertyController->updateRealEstateStatus($itm->property_id,2);
            //echo 'mail sent';                        
        }
    } catch (Exception $e) {
        echo "Mailer Error: " . $e->getMessage();
        //die;
    }
}

$arrayJSON = array();
$arrayJSON['status'] = array('status_code' => "-1", 'status_text' => 'Success.');
$arrayJSON['data'] = $contactResult;
array_walk_recursive($arrayJSON, "convert_to_string");
echo json_encode($arrayJSON);
?>