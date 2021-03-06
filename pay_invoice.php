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
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Pay Invoice</h4>
            <div class="btn-group pull-right">                
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class='col-sm-10 col-md-10'>
                    <div id="paypal-button-container"></div>
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
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
</body>
</html>
<script>
paypal.Button.render({
    // Set your environment
    locale: 'en_US',
    env: 'sandbox', //  | 

    // Specify the style of the button

    style: {
        label: 'checkout',
        size: 'medium', // small | medium | large | responsive
        shape: 'rect', // pill | rect
        color: 'gold'      // gold | blue | silver | black
    },
    // PayPal Client IDs - replace with your own
    // Create a PayPal app: https://developer.paypal.com/developer/applications/create

    client: {
        sandbox: 'AaqAVtcYQu7FqhVDYzY1NNKVqOYXWK3L7tfsl5KrSQVPkrvbX-13t1lQSZM7BE_a8K-YtspdOhRW-z_h',
        production: ''
    },
    payment: function (data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [
                    {
                        amount: {total: '<?php echo $reservedProperty->price ?>', currency: 'USD'}
                    }
                ]
            }
        });
    },
    onAuthorize: function (data, actions) {
        return actions.payment.execute().then(function (data) {
            add_order(data);
        });
    }

}, '#paypal-button-container');

function add_order(data) {  
    data.reserverd_property_id = '<?php echo $reservedProperty->id ?>';
    data.transaction_amount = '<?php echo $reservedProperty->price ?>';
    $.ajax({
        type: "POST",
        url: "add_order.php",
        dataType: "JSON",
        data: data,
        success: function (response) {
            window.location.href = 'pay_invoice_success.php';
        }
    });
}


</script>