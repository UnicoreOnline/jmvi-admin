<?php

require_once 'header.php';
$controller = new ControllerReservedProperty();

$reservedProperty = [];
$desired_dir = Constants::IMAGE_UPLOAD_DIR;
$invoiceBaseUrl = Constants::ROOT_URL .$desired_dir.'/invoice/';
$extras = new Extras();
if (!empty($_SERVER['QUERY_STRING'])) {
    
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    
    if (isset($id) && $id > 0) {
        $reservedProperty = $controller->getReservedPropertyById($id);        
    }

} else {
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

<div class="container">

    <!-- Example row of columns -->
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Invoice</h4>
            <div class="btn-group pull-right">                
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class='col-sm-10 col-md-10'>
                    <?php if(isset($reservedProperty->invoice) && !empty($reservedProperty->invoice)){ ?>
                    <iframe src="<?= $invoiceBaseUrl.$reservedProperty->invoice ?>" width='100%' height="500px"></iframe>
                    <?php } ?>
                </div>
                <div class='col-sm-2 col-md-2'>
                    <div class="row">
                        <a class="btn btn-info" target="_blank" href="download_file.php?file=<?= $reservedProperty->invoice ?>" role="button">Download</a>
                        <?php /*<a class="btn btn-info" target="_blank" href="<?php echo $invoiceBaseUrl.$reservedProperty->invoice;?>" role="button" download>Dowland</a> */ ?>
                    </div>
                    <div class="row"><br /></div>
                    <div class="row">
                        <?php $mailUrl = $extras->encryptQuery1(KEY_SALT, 'id', $reservedProperty->id, 'send_invoice_email.php'); ?>
                        <a class="btn btn-info" href="<?= $mailUrl ?>" role="button">Send</a>
                    </div>                    
                </div>
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