<?php 

require_once "/var/www/html/project_new/PHPMailer/PHPMailerAutoload.php";


class Email {
	
	public $mail;
	public $messageFlag;
	
	public function __construct()
	{
		$mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "jmviapp@gmail.com";
        $mail->Password = "investmentjmvi";
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        $mail->From = "jmviapp@gmail.com";
        $mail->FromName = "JMVI RealEstate";
        $mail->addReplyTo('investmentjmvi@gmail.com', "JMVI RealEstate");
        $mail->isHTML(true);
		$this->mail = $mail;
		$this->messageFlag = 1;	
	}
	
	public function sendEmail($email , $name , $subject, $body = "", $attachment = "", $extraAttachments = [])
	{
		$this->mail->addAddress($email, $name);
        if(!empty($attachment)) {
			$this->mail->addAttachment("/var/www/html/project_new/upload_pic/invoice/".$reservedProperty->invoice);
		}
		// add extra attachment
		if(!empty($extraAttachments)) {
			foreach($extraAttachments as $extraAttachment) {
				$this->mail->addAttachment($extraAttachment);
			}
		}		
		
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
		
		try{
            if (!$this->mail->send()) {
                $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
                return false;
            }else {

            	if ($this->messageFlag == 2) {
                	$_SESSION['message'] = 'An email was sent to the recipient letting the user know that you have approved this property.';            		
            	}
                return true;
            }
        }catch(Exception $e) {
            $_SESSION['error'] = "Mailer Error: " . $e->getMessage();
            return false;
        } 
	}	
}	
	
	