<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 12/4/18
 * Time: 1:20 AM
 */
//var_dump($_GET); die;
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once 'header.php';
require_once "PHPMailer/PHPMailerAutoload.php";
$extras = new Extras();
$userController = new ControllerUser();
$propertyController = new ControllerRealEstate();
$controller = new ControllerReservedProperty();
$agent = new ControllerAgent();



if (!isset($_GET['user_id']) && !isset($_GET['property_id'])) {
//This page should not be accessed directly. Need to submit the form.
    echo "error; invalid request!";
    die;
}
$user = $userController->getUserByUserId($_GET['user_id']);
$property = $propertyController->getRealEstateByRealEstateId($_GET['property_id']);

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
$controller->insertReservedProperty($itm);
    
if(!empty($property) && !empty($property->agent_id)) {

    $agentDetail = $agent->getAgentByAgentId($property->agent_id);

    if(!empty($agentDetail)) {
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
        $mail->addAddress('investmentjmvi@gmail.com', $senderName);
        $mail->addReplyTo($email, $userName);
        //$mail->AddCC('investmentjmvi@gmail.com', $senderName);
        $mail->isHTML(true);
        $mail->Subject = "Reservation - JMVI";
        $mail->Body = "Hi,<br/><br/><br/>
        {$userName} has reserved the {$propertyName} at ".date('H:i',strtotime($itm->created_at))." on ".date('Y-m-d',strtotime($itm->created_at))."</br><br/><br/>
        Please contact {$userName} at {$mobile} or {$email} within the hour to finalize arrangement on property.</br><br/><br/>
        Thank you.";
        try{
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
                die;
            }else {
                // no need to update status once property reserved
                //$propertyController->updateRealEstateStatus($itm->property_id,2);
                echo 'mail sent';
                die;
            }

        }catch(Exception $e) {
            echo "Mailer Error: " . $e->getMessage();
            die;
        } 
    }
 }     

echo "Done";
die;
//var_dump($property); die;



/*$mail->Body = "
<p>
User {$userName} just reserved a property. See details below. 
</p> 
<br />
<p>
Name of Property : {$propertyName}
</p>
<br />
<p>
Address: {$address}
</p>
<br />
<p>
Name: {$userName}
</p>
<br />
<p>
Email: {$email}
</p>
<br />
<p>
Mobile: {$mobile}
</p>
<br />
<b>Contact User for processing transaction.</b>
";*/








