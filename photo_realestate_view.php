<?php

require_once 'header.php';
$controller = new ControllerPhoto();
$controllerRealEstate = new ControllerRealEstate();

$extras = new Extras();
$realestate_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
$photo_realestate_delete = $extras->decryptQuery2(KEY_SALT, $_SERVER['QUERY_STRING']);

if ($realestate_id != null) {
    $photos_realestate = $controller->getPhotosByRealEstateId($realestate_id);
    $selected_realestate = $controllerRealEstate->getRealEstateByRealEstateId($realestate_id);
}

if ($photo_realestate_delete != null) {
    $realestate_id = $photo_realestate_delete[0];
    $photo_id = $photo_realestate_delete[1];
    $controller->deletePhoto($photo_id, 1);

    $viewUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate_id, 'photo_realestate_view.php');
    echo "<script type='text/javascript'>location.href='$viewUrl';</script>";
}

if ($realestate_id == null && $photo_realestate_delete == null) {
    echo "<script type='text/javascript'>location.href='403.php';</script>";
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

    <title>JMVI Real Estate</title>

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
<?php 
	$endUrl = "realestates.php?stype=".$selected_realestate->status;
	if($selected_realestate->status == 3) {
		$endUrl = "auction.php";
	}	
?>
<div class="container">

    <!-- Example row of columns -->
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Real Estate Photos</h4>
            <div class="btn-group pull-right">
                <form method="POST" action="">
                    <a href="<?php echo $endUrl; ?>" class="btn btn-default btn-sm"><span
                                class='glyphicon glyphicon-arrow-left'></span></a>

                    <?php
                    $insertUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $realestate_id, 'photo_realestate_insert.php');
                    echo "<a href='$insertUrl' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-plus'></span></a>";
                    ?>

                </form>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">

                <?php
                if ($photos_realestate != null) {

                    $ind = 1;
                    $count = count($photos_realestate);
                    foreach ($photos_realestate as $photo_realestate) {

                        $extras = new Extras();
                        $updateUrl = $extras->encryptQuery1(KEY_SALT, 'photo_id', $photo_realestate->photo_id, 'photo_realestate_update.php');
                        $deleteUrl = $extras->encryptQuery2(KEY_SALT, 'realestate_id', $photo_realestate->realestate_id, 'photo_id', $photo_realestate->photo_id, 'photo_realestate_view.php');

                        echo "<div class='col-sm-6 col-md-4'>";
                        echo "<div class='thumbnail'>";
                        echo "<img src='$photo_realestate->thumb_url' alt='...' style = 'display:block; height:150px; margin-left:auto; margin-right:auto; max-width:100%;'>";
                        echo "<div class='caption'>";
                        echo "<p>";
                        echo "<a href='$updateUrl' class='btn btn-primary btn-xs' role='button'>Edit</a> ";
                        echo "<button  class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_$photo_realestate->photo_id'>Large Photo</button> ";
                        echo "<button  class='btn btn-primary btn-xs' data-toggle='modal' data-target='#delete_modal_$photo_realestate->photo_id'>Delete</button>";
                        echo "</p>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";


                        //<!-- Modal -->
                        echo "<div class='modal fade' id='modal_$photo_realestate->photo_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>";
                        echo "<h4 class='modal-title' id='myModalLabel'>$ind/$count Photo(s)</h4>";
                        echo "</div>";

                        echo "<div class='modal-body'>";
                        echo "<img src='$photo_realestate->photo_url' style = 'display:block; height:100%; margin-left:auto; margin-right:auto; max-width:100%;'/>";
                        echo "</div>";

                        echo "</div>";
                        echo "</div>";
                        echo "</div>";


                        //<!-- Modal -->
                        echo "<div class='modal fade' id='delete_modal_$photo_realestate->photo_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                                  <div class='modal-dialog'>
                                                      <div class='modal-content'>
                                                          <div class='modal-header'>
                                                                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                                <h4 class='modal-title' id='myModalLabel'>Deleting Real Estate Photo </h4>
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


            </div>

        </div><!--/.panel-body -->

    </div>


</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="bootstrap/js/jquery.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>


</body>
</html>