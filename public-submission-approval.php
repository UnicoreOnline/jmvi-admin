<?php

require_once 'header.php';
require_once 'library/Email.php';
$controller = new ControllerRealEstate();
$controllerPhoto = new ControllerPhoto();


$searchPrams = [];
$ptype = 4;
$searchPrams["status"] = $ptype;
$_GET['stype'] = 4;

$realestates = $controller->getRealEstatesBySearching($searchPrams);

$search_criteria = "";
if (isset($_POST['button_search'])) {    
    $searchPrams['search_text'] = trim(strip_tags($_POST['search'])); 
    $realestates = $controller->getRealEstatesBySearching($searchPrams);
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
    <link rel="shortcut icon" href="bootstrap/images/16px_JMVI_logo.png">

    <title>RealEstate JMVI Real Estate</title>

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

<!-- Fixed navbar -->
<?php require_once 'menu.php'; ?>

<div class="container">

    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Public Submission Approval</h4>
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
						$defaulyPtype = 2;
						if($ptype == 4) {
						   $defaulyPtype = 4;
						}	
                        $excelUrl = $extras->encryptQuery1(KEY_SALT, 'ref_id', $ptype == 0 ?  1: $defaulyPtype, 'download_excel.php');
                    ?>
                    <a href="<?= $excelUrl ?>"  class="btn btn-default btn-sm"><span
                                class='glyphicon glyphicon-download-alt'></span></a>
                    <a href="realestate_insert.php?stype=<?= $ptype ?>" class="btn btn-default btn-sm"><span
                                class='glyphicon glyphicon-plus'></span></a>
                </form>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
            <tr>
                <th>Name of Property</th>
                <th>Name</th>
                <th>Telephone</th>
                <th>Email</th>
                <th>Description</th>
                <th>Address</th>
                <th>Country</th>
                <th>Property Type</th>
                <th>Price</th>                
                <th>Beds</th>
                <th>Baths</th>
                <th>Property Size (Sq. Ft)</th>
                <th>Rooms</th>
                <th>Lot Size (Sq. Ft.)</th>
                <th>Built In</th>
                <th>Photo Gallery</th>
                <th></th>
            </tr>

            </thead>
            <tbody>
            <?php

            if ($realestates != null) {

                $ind = 1;
                foreach ($realestates as $realestate) {
                    
                    $featured = "No";
                    if ($realestate->featured == 1)
                        $featured = "Yes";

                    $no_of_photos = $controllerPhoto->getNoOfPhotosByRealEstateId($realestate->realestate_id);

                    $extras = new Extras();
                    $declineUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate->realestate_id,'decline-public-submission.php');
                    $acceptUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate->realestate_id,'accept-public-submission.php');
                    $photoUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate->realestate_id, 'photo_realestate_insert.php');
					$viewUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate->realestate_id, 'photo_realestate_view.php');

                    echo "<tr>";
                    echo "<td>$realestate->pname</td>";                                        
                    echo "<td>$realestate->name</td>";                
                    echo "<td>$realestate->telephone</td>";                               
                    echo "<td>$realestate->email</td>";                                        
                    echo "<td><a href='#'  data-toggle='popover' data-placement='bottom' data-content='".$realestate->pdes."'>".substr($realestate->pdes,0,20)."...</a></td>";
                    echo "<td>$realestate->address</td>";
                    echo "<td>$realestate->country</td>";
                    echo "<td>$realestate->property_type_str</td>";
                    //echo "<td>".$realestate->currency.' '.$realestate->price."</td>";
                    echo "<td>".$realestate->currency.' '.'$'.number_format(str_replace(',','',$realestate->price),2)."</td>";
                    //echo "<td>".$realestate->currency.' '.$realestate->price_per_sqft."</td>";
                    //echo "<td>".'$'.number_format($realestate->price_per_sqft,2)."</td>";
                    echo "<td>$realestate->beds</td>";
                    echo "<td>$realestate->baths</td>";
                    echo "<td>$realestate->sqft</td>";
                    echo "<td>$realestate->rooms</td>";
                    echo "<td>$realestate->lot_size</td>";
                    echo "<td>$realestate->built_in</td>";
                    echo "<td><a href='".$viewUrl."' target='_blank'>Click here to view</a></td>";


                    echo "<td>
                                    <a class='btn  btn-xs' href='$acceptUrl'><span class='glyphicon glyphicon-ok-circle'></span></a>
                                    <a href='javascript:void(0);'  class='btn  btn-xs' data-toggle='modal' data-target='#modal_$realestate->realestate_id'><span class='glyphicon glyphicon-ban-circle'></span></a>                                    
                                    
                                </td>";
                    echo "</tr>";


                    //<!-- Modal -->
                    echo "<div class='modal fade' id='modal_$realestate->realestate_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Decline Real Estate</h4>
                                              </div>
                                              <div class='modal-body'>
                                                    <p>Do you wish to continue?
                                              </div>
                                              <div class='modal-footer'>
                                                  <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                  <a type='button' class='btn btn-primary' href='$declineUrl'>Decline</a>
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