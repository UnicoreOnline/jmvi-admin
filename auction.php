<?php
require_once 'header.php';
$controller = new ControllerAuction();
$realestate = new ControllerRealEstate();
$controllerPhoto = new ControllerPhoto();



if (!empty($_SERVER['QUERY_STRING'])) {

    $extras = new Extras();
    $params = $extras->decryptQuery2(KEY_SALT, $_SERVER['QUERY_STRING']);
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);

    
    
    $auction_id = $params[0];
    $start_bid = $params[1] == 0 ? 1 : 0;

    if ($params != null) {
        $controller->updateAuctionStartBid($auction_id, $start_bid);
        echo json_encode(['status'=>'200','message'=>'Success']);
        exit;        
    } else {
		
		if ($id != null) {
			$controller->deleteAuction($id, 1);
			echo "<script type='text/javascript'>location.href='auction.php';</script>";
		}
		
        echo json_encode(['status'=>'500','message'=>'Fail']);
        exit;        
    }
    

}

$search_criteria = "";
if (isset($_POST['button_search'])) {
    $searchParam = [
        'search' => trim(strip_tags($_POST['search']))
    ];    
    $auctions = $controller->getAuction($searchParam);
} else {
    $auctions = $controller->getAuction();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Auction JMVI Real Estate</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="bootstrap/css/navbar-fixed-top.css" rel="stylesheet">
    <link href="bootstrap/css/custom.css" rel="stylesheet">


    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<?php require_once 'menu.php'; ?>
<style>
/*
.switch_button#button-10 .knobs:before{
	background-color:green !important;
}*/	
</style>
<div class="container">

    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Real Estate (Auction)</h4>
            <div class="btn-group pull-right">
                <!-- <a href="car_insert.php" class="btn btn-default btn-sm">Add Car</a> -->
                <form method="POST" action="">
                    <input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search"
                           name="search" value="<?php echo $search_criteria; ?>">
                    <button type="submit" name="button_search" class="btn btn-default btn-sm"><span
                                class="glyphicon glyphicon-search"></span></button>
                    <button type="submit" class="btn btn-default btn-sm" name="reset"><span
                                class="glyphicon glyphicon-refresh"></span></button>
                    <?php
                        $extras = new Extras();
                        $excelUrl = $extras->encryptQuery1(KEY_SALT, 'ref_id', 3, 'download_excel.php');
                    ?>
                    <a href="<?= $excelUrl ?>"  class="btn btn-default btn-sm"><span
                                class='glyphicon glyphicon-download-alt'></span></a>
                    <a href="auction_insert.php" class="btn btn-default btn-sm"><span
                                class='glyphicon glyphicon-plus'></span></a>
                </form>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name of Property</th>
                    <th>Description</th>
                    <th>Country</th>
                    <th>Address</th>
                    <th>Property Type</th>
                    <th>Starting Bid</th>
                    <th>Property Value</th>
                    <th>Photo Gallery</th>
                    <th>Real Estate Agent</th> 
                    <th>Beds</th>
                    <th>Baths</th>
                    <th>Property Size (Sq. Ft.)</th>
                    <th>Rooms</th>
                    <th>Lot Size (Sq. Ft.)</th>
                    <th>Built In</th>
                    <th>Feature</th>
                    <th>Start Time</th>
                    <th>Number of Bid(s)</th>
                    <th>Highest Bid</th>
                    <th>Start / End Bid</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php

            if ($auctions != null) {

                $ind = 1;
                foreach ($auctions as $auction) {
                    
                    //$no_of_photos = $controllerPhoto->getNoOfPhotosByRealEstateId($auction->property_id);
                    $extras = new Extras();
                    $updateUrl = $extras->encryptQuery1(KEY_SALT, 'id', $auction->id, 'auction_update.php');
                    $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'id', $auction->id, 'auction.php');                    
                    $viewUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $auction->property_id, 'photo_realestate_view.php');
                    $bidUrl = $extras->encryptQuery1(KEY_SALT, 'auction_id', $auction->id, 'bid_by_auction.php');
                    
                    $featuredUrl = $extras->encryptQuery2(KEY_SALT, 'id', $auction->id, 'id', $auction->is_start_bid, 'auction.php');
                    //$photoUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $auction->property_id, 'photo_realestate_insert.php');
                    $featured = "No";
                    if ($auction->featured == 1){
                        $featured = "Yes";
                    }
                    
                    echo "<tr>";
                    echo "<td>$auction->id</td>";                    
                    echo "<td>$auction->pname</td>";                    
                    echo "<td><a href='#' data-toggle='popover' data-placement='bottom' data-content='".$auction->pdes."'>".substr($auction->pdes,0,20)."...</a></td>";
                    echo "<td>$auction->country</td>";                    
                    echo "<td>$auction->address</td>";                    
                    echo "<td>$auction->property_type_str</td>";
                    echo "<td>".$auction->currency.' '.'$'.number_format($auction->starting_bid,2)."</td>";
                    //echo "<td>".$auction->currency.' '.$auction->price."</td>";
                    //echo "<td>".$auction->currency.' '.$auction->price_per_sqft."</td>";
                    echo "<td>".$auction->currency.' '.'$'.number_format($auction->price_per_sqft,2)."</td>";
                    echo "<td><a href='".$viewUrl."' target='_blank'>Click here to view</a></td>";
                    echo "<td>$auction->agent_name</td>";
                    echo "<td>$auction->beds</td>";
                    echo "<td>$auction->baths</td>";
                    echo "<td>$auction->sqft</td>";
                    echo "<td>$auction->rooms</td>";
                    echo "<td>$auction->lot_size</td>";
                    echo "<td>$auction->built_in</td>";
                    echo "<td>$featured</td>";
                    echo "<td>".date('h:i:s A',strtotime($auction->start_time))."</td>";
                    echo "<td><a href='".$bidUrl."'>".$auction->total_bid."</a></td>";
                    echo "<td>$".$auction->highest_bid."</td>";

                    echo "<td><div class='button b2 switch_button' id='button-10'>
                                <input type='checkbox' ".($auction->is_start_bid == 1 ? 'checked':'')." class='checkbox start_bid' data-url='".$featuredUrl."'>
                                <div class='knobs'>
                                        <span>On</span>
                                </div>
                                <div class='layer'></div>
                        </div></td>";                    
                    echo "<td>
                                    <a class='btn  btn-xs' href='$updateUrl'><span class='glyphicon glyphicon-pencil'></span></a>
                                    <a href='javascript:void(0);' class='btn  btn-xs' data-toggle='modal' data-target='#modal_$auction->id'><span class='glyphicon glyphicon-remove'></span></a>
                                </td>";
                    echo "</tr>";

                    echo "<div class='modal fade' id='modal_$auction->id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Deleting Property Type</h4>
                                              </div>
                                              <div class='modal-body'>
                                                    <p>Deleting this is not irreversible. Do you wish to continue?
                                              </div>
                                              <div class='modal-footer'>
                                                  <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                  <a type='button' class='btn btn-primary' href='$deleteUrl'>Delete</a>
                                              </div>
                                          </div>
                                      </div>
                                </div>";

                    ++$ind;
                }
            }

            ?>
            </tbody>

        </table>
    </div>

</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="bootstrap/js/jquery.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script>
    $( document ).ready(function() {
        $(".start_bid").change(function () {
            var accessUrl = $(this).attr('data-url');
            $.ajax({
                type: "GET",
                url: accessUrl,
                async: true,
                data: {
                    //action1: value // as you are getting in php $_POST['action1'] 
                },
                success: function (msg) {
                    var msg = JSON.parse(msg);
                    
                    if(msg.status == '200'){
                        alert('Auction Updated Successfully.');
                        //window.location.href='users.php';
                    } else {
                        alert('Something went wrong.');
                    }
                }
            });
        });
    });    
    
</script>
<script>     
    $(function () {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus',
            html : true,
            title : 'Description <a href="#" class="close" data-dismiss="alert">&times;</a>',
        });
        $(document).on("click", ".popover .close" , function(){
            $(this).parents(".popover").popover('hide');
        });
    })
</script>

</body>
</html>