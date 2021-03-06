<?php

require_once 'header.php';
$controller = new ControllerBanner();


$extras = new Extras();
$banner_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
$banner_delete = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);


if ($banner_delete != null) {
    
    $controller->deleteBanner($banner_id, 1);
    echo "<script type='text/javascript'>location.href='banner_list.php';</script>";
}


$banners = $controller->getBanner();
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

<div class="container">

    <!-- Example row of columns -->
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Paid Advertisement Banner</h4>
            <div class="btn-group pull-right">  
                <form method="POST" action="">
                    <?php                    
                    echo "<a href='banner_add.php' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-plus'></span></a>";
                    ?>

                </form>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">

                <?php
                if ($banners != null) {

                    $ind = 1;
                    $count = count($banners);
                    foreach ($banners as $banner) {

                        $extras = new Extras();
                        $updateUrl = $extras->encryptQuery1(KEY_SALT, '$banner', $banner->id, 'banner_update.php');
                        $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'banner_id', $banner->id, 'banner_list.php');

                        echo "<div class='col-sm-6 col-md-4'>";
                        echo "<div class='thumbnail'>";
                        echo "<img src='$banner->banner_url' alt='...' style = 'display:block; height:150px; margin-left:auto; margin-right:auto; max-width:100%;'>";
                        echo "<div class='caption'>";
                        echo "<p>";
                        echo "<a href='$updateUrl' class='btn btn-primary btn-xs' role='button'>Edit</a> ";
                        echo "<button  class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_$banner->id'>Large Photo</button> ";
                        echo "<button  class='btn btn-primary btn-xs' data-toggle='modal' data-target='#delete_modal_$banner->id'>Delete</button>";
                        echo "</p>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";


                        //<!-- Modal -->
                        echo "<div class='modal fade' id='modal_$banner->id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>";
                        echo "<h4 class='modal-title' id='myModalLabel'>$ind/$count Photo(s)</h4>";
                        echo "</div>";

                        echo "<div class='modal-body'>";
                        echo "<img src='$banner->banner_url' style = 'display:block; height:100%; margin-left:auto; margin-right:auto; max-width:100%;'/>";
                        echo "</div>";

                        echo "</div>";
                        echo "</div>";
                        echo "</div>";


                        //<!-- Modal -->
                        echo "<div class='modal fade' id='delete_modal_$banner->id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                                  <div class='modal-dialog'>
                                                      <div class='modal-content'>
                                                          <div class='modal-header'>
                                                                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                                <h4 class='modal-title' id='myModalLabel'>Deleting Banner </h4>
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