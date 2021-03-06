<?php

require_once 'header.php';
$controller = new ControllerRealEstate();
$controllerPhoto = new ControllerPhoto();

$searchArray = [
    'featured' => 1
];

$realestates = $controller->getRealEstatesBySearching($searchArray);

$search_criteria = "";
if (isset($_POST['button_search'])) {    
    $searchArray['search_text'] = trim(strip_tags($_POST['search'])); 
    $realestates = $controller->getRealEstatesBySearching($searchArray);
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
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Featured Property</h4>
            <div class="btn-group pull-right">
                <!-- <a href="car_insert.php" class="btn btn-default btn-sm">Add Car</a> -->
                <form method="POST" action="">
                    <input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search"
                           name="search" value="<?php echo $search_criteria; ?>">
                    <button type="submit" name="button_search" class="btn btn-default btn-sm"><span
                                class="glyphicon glyphicon-search"></span></button>
                    <button type="submit" class="btn btn-default btn-sm" name="reset"><span
                                class="glyphicon glyphicon-refresh"></span></button>                    
                </form>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
            <tr>
                <th>Name of Property</th>
                <th>Description</th>
                <th>Address</th>
                <th>Location</th>
                <th>Property Type</th>
<!--                <th>Property Value</th>-->
                <th>Price</th>
                <th>Beds</th>
                <th>Baths</th>
                <th>Sq Ft</th>
                <th>Rooms</th>
                <th>Lot Size</th>
                <th>Built In</th>
                <th>Photo Gallery</th>
                <th>Real Estate Agent</th> 
            </tr>

            </thead>
            <tbody>
            <?php

            if ($realestates != null) {

                $ind = 1;
                foreach ($realestates as $realestate) {
                    $no_of_photos = $controllerPhoto->getNoOfPhotosByRealEstateId($realestate->realestate_id);

                    $extras = new Extras();                    
                    $viewUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate->realestate_id, 'photo_realestate_view.php');
                    

                    echo "<tr>";
                    //echo "<td>$ind</td>";
                    echo "<td>$realestate->pname</td>";                                        
                    echo "<td><a href='#' data-toggle='popover' data-placement='bottom' data-content='".$realestate->pdes."'>".substr($realestate->pdes,0,20)."...</a></td>";
                    echo "<td>$realestate->address</td>";
                    echo "<td>$realestate->location</td>";
                    echo "<td>$realestate->property_type_str</td>";
                    echo "<td>".$realestate->currency.' '.'$'.number_format($realestate->price,2)."</td>";
                    //echo "<td>".'$'.number_format($realestate->price_per_sqft,2)."</td>";
                    echo "<td>$realestate->beds</td>";
                    echo "<td>$realestate->baths</td>";
                    echo "<td>$realestate->sqft</td>";
                    echo "<td>$realestate->rooms</td>";
                    echo "<td>$realestate->lot_size</td>";
                    echo "<td>$realestate->built_in</td>";
                    echo "<td><a href='".$viewUrl."' target='_blank'>Click here to view</a></td>";
                    echo "<td>$realestate->agent_name</td>";
                    echo "</tr>";

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