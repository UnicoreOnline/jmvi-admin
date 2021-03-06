<?php session_start();
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
$controller = new ControllerBidMade();
$agent = new ControllerAgent();

$desired_dir = Constants::IMAGE_UPLOAD_DIR;
$invoiceBaseUrl = Constants::ROOT_URL .$desired_dir.'/invoice/';
$extras = new Extras();
$bidMade = [];
if (!empty($_SERVER['QUERY_STRING'])) {
    
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    
    if (isset($id) && $id > 0) {          
        $bid = $controller->getBidById($id);        
        $bidMade = $controller->getBidMadeById($id);
    }

} else {
    echo "<script type='text/javascript'>location.href='403.php';</script>";
}
$user = $userController->getUserByUserId($bidMade->user_id);
if(isset($bid['property_id']) && !empty($bid['property_id'])){
    $property = $propertyController->getRealEstateByRealEstateId($bid['property_id']);
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

//$payUrl = "https://www.paypal.com/webapps/shoppingcart?flowlogging_id=11aa5ad67fd4e&mfid=1584044876111_11aa5ad67fd4e#/checkout/openButton";//$extras->encryptQuery1(KEY_SALT, 'id', $reservedProperty->id, 'pay_invoice.php');
$payUrl = "https://PayPal.me/jmvirealty";

if(!empty($user) && !empty($user->user_id)) {
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
        $mail->addAddress($email, $userName);
        $mail->addReplyTo('investmentjmvi@gmail.com', $senderName);        
        $mail->addAttachment("upload_pic/invoice/".$bidMade->invoice);
        $mail->isHTML(true);
        $mail->Subject = "JMVI property";
        $mail->Body = "Hi ".$userName.",<br/><br/>
        Congratulations! You have made bid for {$propertyName}. We have attached the invoice for payment. Click <a href='".$payUrl."'>Pay Now</a> to have payment done through PayPal.
        </br><br/><br/>
        Thank you.";
        
        try{
            if (!$mail->send()) {
               // echo "Mailer Error: " . $mail->ErrorInfo;
                $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
                //die;
            }else {
                // no need to update status once property reserved
                //$propertyController->updateRealEstateStatus($itm->property_id,2);
                //echo 'mail sent';
                $_SESSION['message'] = 'Invoice sent successfully';
                //die;
            }

        }catch(Exception $e) {
            //echo "Mailer Error: " . $e->getMessage();
            $_SESSION['error'] = "Mailer Error: " . $e->getMessage();
            //die;
        } 
    
 }     

header('Location: ' . $_SERVER['HTTP_REFERER']);