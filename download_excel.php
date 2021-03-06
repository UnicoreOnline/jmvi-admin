<?php
require_once 'header.php';
require_once('third_party/PHPExcel.php');

$controller = new ControllerRealEstate();
$controllerAuction = new ControllerAuction();
$controllerRP = new ControllerReservedProperty();
$controllerBM = new ControllerBidMade();
$userController = new ControllerUser();

$extras = new Extras();
$ref_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
$records = [];

if ($ref_id != null) {

    // filename for download
    if ($ref_id == 1) {
        $filename = "rental_space_" . date('Ymd') . ".xls";
        $realestates = $controller->getRealEstatesBySearching(['status' => 0]);
        $records = [];
        foreach ($realestates as $itm){
            $records[] = [
                'Name of Property' => $itm->pname,
                'Description' => $itm->pdes,
                'Address' => $itm->address,
                'Country' => $itm->country,
                'Property Type' => $itm->property_type_str,
                'Price per month' => $itm->price_per_month,
                'Bedroom' => $itm->beds,
                'Bathroom' => $itm->baths,
                'Rooms' => $itm->rooms,
                'Property Size (Sq. Ft) ' => $itm->sqft,
                'Lot Size (Sq. Ft.)' => $itm->lot_size,
                'Built In' => $itm->built_in,
                'Currency' => $itm->currency,
                'Contact for Price' => $itm->is_contact_price,
                'Latitude' => $itm->lat,
                'Longitude' => $itm->lon,
                'Real Estate Agent' => $itm->agent_name,
                'Feature' => $itm->featured == 1 ? 'Yes':'No',
                //'Status' => $itm->status,                                    
            ];
        }
    } else if ($ref_id == 2) {
        $filename = "sale_space_" . date('Ymd') . ".xls";
        $realestates = $controller->getRealEstatesBySearching(['status' => 1]);
        $records = [];
        foreach ($realestates as $itm){
            $records[] = [
                'Name of Property' => $itm->pname,
                'Description' => $itm->pdes,
                'Address' => $itm->address,
                'Country' => $itm->country,
                'Property Type' => $itm->property_type_str,
                'Price' => $itm->price_per_month,
                'Bedroom' => $itm->beds,
                'Bathroom' => $itm->baths,
                'Rooms' => $itm->rooms,
                'Property Size (Sq. Ft) ' => $itm->sqft,
                'Lot Size (Sq. Ft.)' => $itm->lot_size,
                'Built In' => $itm->built_in,
                'Currency' => $itm->currency,
                'Contact for Price' => $itm->is_contact_price,
                'Latitude' => $itm->lat,
                'Longitude' => $itm->lon,
                'Real Estate Agent' => $itm->agent_name,                
                'Feature' => $itm->featured == 1 ? 'Yes':'No',
                //'Status' => $itm->status,                                    
            ];
        }
        
    } else if ($ref_id == 3) {
        $filename = "auction_" . date('Ymd') . ".xls";
        $auctions = $controllerAuction->getAuction();
        $records = [];
        
        foreach ($auctions as $itm){
            $records[] = [
                'Name of Property' => $itm->pname,
                'Description' => $itm->pdes,                
                'Country' => $itm->country,                
                'Address' => $itm->address,                
                'Property Type' => $itm->property_type_str,                    
                'Asking Price' => $itm->currency.' '.'$'.number_format($itm->starting_bid,2),                
                'Property Value' => $itm->currency.' '.'$'.number_format($itm->price_per_sqft,2),
                'Real Estate Agent' => $itm->agent_name,
                'Bedroom' => $itm->beds,
                'Bathroom' => $itm->baths,                
                'Property Size (Sq. Ft)' => $itm->sqft,                
                'Rooms' => $itm->rooms,
                'Lot Size (Sq. Ft.)' => $itm->lot_size,
                'Built In' => $itm->built_in,
                'Feature' => $itm->featured == 1 ? 'Yes':'No', 
                'Start Time' => date('h:i:s A',strtotime($itm->start_time)),
                'Number of Bid(s)' => $itm->total_bid,
                'Highest Bid' => '$'.$itm->highest_bid,      
                'Start / End Bid' => $itm->is_start_bid == 1 ? 'Yes':'No',                                    
                                         
            ];
        }
    } else if ($ref_id == 4) {
        $filename = "approval_list_" . date('Ymd') . ".xls";
        $realestates = $controller->getRealEstatesBySearching(['status' => 4]);
        $records = [];
        foreach ($realestates as $itm){
            $records[] = [
                'Name of Property' => $itm->pname,
                'Email' => $itm->email,
                'Description' => $itm->pdes,
                'Address' => $itm->address,
                'Country' => $itm->country,
                'Property Type' => $itm->property_type_str,
                'Price' => $itm->price,
                'Bedroom' => $itm->beds,
                'Bathroom' => $itm->baths,
                'Rooms' => $itm->rooms,
                'Property Size (Sq. Ft) ' => $itm->sqft,
                'Lot Size (Sq. Ft.)' => $itm->lot_size,
                'Built In' => $itm->built_in,
                'Currency' => $itm->currency,
                'Latitude' => $itm->lat,
                'Longitude' => $itm->lon,
                'Real Estate Agent' => $itm->agent_name
                //'Status' => $itm->status,                                    
            ];
        }
    } else if ($ref_id == 5) {
        $filename = "register_bidders" . date('Ymd') . ".xls";

        if (isset($_SESSION['properties']) && !empty($_SESSION['properties'])) {
            $properties = unserialize($_SESSION['properties']);    
        } else {
            $bidders = $controllerRP->getGroupedReservedProperty(1,'',true);
            $properties = isset($bidders['records']) ? $bidders['records'] : null;            
        }

        $records = [];
        
        if (!empty($properties)) {
            foreach ($properties as $itm){
                $records[] = [
                    'User Name' => $itm->user_name,
                    'Contact Number' => $itm->mobile,                
                    'Email Address' => $itm->user_email,                
                    'User Address' => $itm->user_address,                
                    'Property Name' => $itm->property_name,                    
                    'Created Date' => $itm->created_at,
                    'Bid Allow' => $itm->is_allowed == 1 ? 'Yes' : 'No'     
                ];
            }            
        }
    } else if ($ref_id == 6) {
        $filename = "bids_made" . date('Ymd') . ".xls";

        if (isset($_SESSION['bids_made']) && !empty($_SESSION['bids_made'])) {
            $bidders = unserialize($_SESSION['bids_made']);    
        } else {
            $bidders = $controllerBM->getBidMade();
        }
        
        $records = [];
        
        if (!empty($bidders)) {
            foreach ($bidders as $itm){

                if ($itm->user_id) {
                    $user = $userController->getUserByUserId($itm->user_id);
                }

                if ($itm->auction_id) {

                    $auction = $controllerAuction->getAuctionByAuctionId($itm->auction_id);
                }
                if (isset($auction) && $auction->property_id) {

                    $property = $controller->getRealEstateByRealEstateId($auction->property_id);
                    
                }
                $records[] = [
                    'Full Name'             => @$user->full_name,
                    'User Address'          => @$user->address,                
                    'Email Address'         => @$user->email,                
                    'Contact Number'        => @$user->mobile,                
                    'Name of Property'      => @$property->pname,                    
                    'Address of Property'   => @$property->address,
                    'Highest Bid'           => '$'.number_format($itm->bid_amount,2),
                    'Date of Bid'           => date("F d, Y", strtotime(str_replace('-','/', $itm->created_at))),
                    'Time of Bid'           => date("h:i:s A", strtotime(str_replace('-','/', $itm->created_at)))
                ];
            }            
        }
    }
   /*
   header("Content-Disposition: attachment; filename=\"$filename\"");
   header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8");

    $flag = false;
    if (!empty($records)) {
        foreach ($records as $row) {                  
            if (!$flag) {
                // display field/column names as a first row
                echo implode("\t", array_keys($row)) . "\n";
                $flag = true;
            }
            echo implode("\t", array_values($row)) . "\n";
        }
    }
    exit;
    * 
    */
    $doc = new PHPExcel();

    // set active sheet 
    $doc->setActiveSheetIndex(0);

    $finalArray = [];
    if(!empty($records)){
        $finalArray[] =  array_keys($records[0]);
        $finalArray = array_merge($finalArray,$records);
    }
    
    // read data to active sheet
    $doc->getActiveSheet()->fromArray($finalArray);

    //mime type
    header('Content-Type: application/vnd.ms-excel');
    //tell browser what's the file name
    header('Content-Disposition: attachment;filename="' . $filename . '"');

    header('Cache-Control: max-age=0'); //no cache
    //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
    //if you want to save it as .XLSX Excel 2007 format

    $objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

    //force user to download the Excel file without writing it to server's HD
    $objWriter->save('php://output');
} else {
    echo "<script type='text/javascript'>location.href='403.php';</script>";
}

function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
}

?>