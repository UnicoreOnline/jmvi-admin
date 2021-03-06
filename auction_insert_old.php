<?php

require_once 'header.php';
$controller = new ControllerAuction();
$controllerRealEstate = new ControllerRealEstate();

$realEstates = $controllerRealEstate->getRealEstates();

$extras = new Extras();
if (isset($_POST['submit'])) {

    $itm = new Auction();
    $itm->property_id = trim(strip_tags($_POST['property_id']));
    $itm->estimate_price = trim(strip_tags($_POST['estimate_price']));
    $itm->starting_bid = trim(strip_tags($_POST['starting_bid']));
    $itm->start_time = trim(strip_tags($_POST['start_time']));
    $itm->end_time = trim(strip_tags($_POST['end_time']));
    $itm->created_at = date('Y-m-d H:i:s');

    $controller->insertAuction($itm);
    echo "<script type='text/javascript'>location.href='auction.php';</script>";

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
    <link rel="shortcut icon" href="http://getbootstrap.com/assets/ico/favicon.ico">

    <title>RealEstate Finder</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="bootstrap/css/navbar-fixed-top.css" rel="stylesheet">
    <link href="bootstrap/css/custom.css" rel="stylesheet">

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&sensor=false"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>


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

    <!-- Example row of columns -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Add Auction</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form action="" method="POST">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Property</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="width:100%;" name="property_id" id="property_id">
                                    <option value="None">Select Property</option>
                                    <?php
                                    if ($realEstates != null) {
                                        foreach ($realEstates as $realEstate) {
                                            echo "<option value='$realEstate->realestate_id'>$realEstate->pname</option>";

                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="estimate_price" class="col-sm-2 col-form-label">Estimate Price</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Estimate Price" name="estimate_price" id="estimate_price"
                                   required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="starting_bid" class="col-sm-2 col-form-label">Starting Bid</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Starting Bid" name="starting_bid" id="starting_bid"
                                   required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="start_time" class="col-sm-2 col-form-label">Start Date (yyyy-mm-dd)</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Start Date (yyyy-mm-dd hh:ii:ss) " name="start_time" id="start_time" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="end_time" class="col-sm-2 col-form-label">End Date (yyyy-mm-dd)</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="End Date (yyyy-mm-dd hh:ii:ss)" name="end_time" id="end_time" required>
                            </div>
                        </div>                        
                        <p>
                            <button type="submit" name="submit" class="btn btn-info" onclick="checkInput()"
                                    role="button">Save
                            </button>
                            <a class="btn btn-info" href="auction.php" role="button">Cancel</a>
                        </p>
                    </form>


                </div>
            </div>
        </div>


    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
    <script>
        function checkInput() {
            var website = document.getElementById("website");
            var details = document.getElementById("details");


            var website = document.getElementById("website");
            var details = document.getElementById("details");

            var strWebsite = website.value.replace("http://", "");
            strFb = strWebsite.replace("https://", "");
            website.value = strWebsite;

            var strDetails = details.value.replace("http://", "");
            strFb = strDetails.replace("https://", "");
            details.value = strDetails;
        }
    </script>


</body>
</html>