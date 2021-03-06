<?php

require_once 'header.php';
require_once 'library/Email.php';
$controller = new ControllerRealEstate();
$controllerPhoto = new ControllerPhoto();


if (!empty($_SERVER['QUERY_STRING'])) {
	$extras = new Extras();
	$realestate_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
	if ($realestate_id != null) {
		$realestate = $controller->getRealEstateByRealEstateId($realestate_id);
		if(!empty($realestate)) {
			$res = $controller->updateRealEstateStatus($realestate_id,99);
			if(!empty($realestate->email)) {
				$emailCls = new Email;
				$emailCls->sendEmail(
					$realestate->email,
					$realestate->name, 
					"Your property has not been approved for listing on the JMVI Realty App",
					"Hi {$realestate->name},<br/><br/>
					your property has not been approved for listing on the JMVI Realty App.<br/><br/>
					If you have any questions, please contact us at (268) 784-JMVI (5684) or investmentjmvi@gmail.com"
				);
			}
		}
	}
}
echo "<script type='text/javascript'>location.href='public-submission-approval.php';</script>";