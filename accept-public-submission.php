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
			$res = $controller->updateRealEstateStatus($realestate_id,1);
			if(!empty($realestate->email)) {
				$emailCls = new Email;
				$emailCls->messageFlag = 2;
				$emailCls->sendEmail(
					$realestate->email,
					$realestate->name, 
					"Your property for sale has been approved on the JMVI Realty App",
					"Hi {$realestate->name},<br/><br/>
					Your property for sale has been approved and will be displayed in the JMVI Realty iOS and Android App"
				);
			}
		}
	}
}
echo "<script type='text/javascript'>location.href='public-submission-approval.php';</script>";