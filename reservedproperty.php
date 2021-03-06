<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 13/4/18
 * Time: 10:21 PM
 */

require_once 'header.php';
$controller = new ControllerReservedProperty();

$searchPrams = [];
if (!empty($_SERVER['QUERY_STRING'])) {

    if(isset($_GET['stype']) && $_GET['stype'] != ''){        
        $searchPrams['status'] = $_GET['stype'];
    }
    
    
    $extras = new Extras();
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    
    if (isset($id) && $id > 0) {
        $controller->deleteReservedProperty($id, 1);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        //echo "<script type='text/javascript'>location.href='reservedproperty.php';</script>";
    }

}
$reservedProperty = $controller->getReservedProperty($searchPrams);
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
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Reserved Property</h4>
            <div class="btn-group pull-right">
                <!-- <a href="car_insert.php" class="btn btn-default btn-sm">Add Car</a> -->
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
            <tr>                
                <th>Full Name</th>
                <th>User Address</th>
                <th>Email Address</th>
                <th>Contact Number</th>                
                <th>Name of Property</th>
                <th>Address of Property </th>
                <th>Photo Gallery</th>
                <th>Asking Price</th>
                <th>Date Reserved</th>
                <th>Time Reserved</th>
                <th>Invoice</th>
                <th></th>
            </tr>

            </thead>
            <tbody>
            <?php
            if ($reservedProperty != null) {
                $ind = 1;
                foreach ($reservedProperty as $data) {
                    if ($data->property_status == 0) {
                        $status = "For Rent";
                    } elseif ($data->property_status == 1) {
                        $status = "For Sale";
                    } elseif ($data->property_status == 2) {
                        $status = "Sold";
                    } else {
                        $status = "Auction";
                    }
                    $extras = new Extras();
                    $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'id', $data->id, 'reservedproperty.php');
                    $viewUrl = $extras->encryptQuery1(KEY_SALT, 'realestate_id', $data->property_id, 'photo_realestate_view.php');
                    
                    $invoiceUrl = $extras->encryptQuery1(KEY_SALT, 'reserved_property_id', $data->id, 'generate_invoice.php');
                    if(!empty($data->invoice)){
                        $invoiceUrl = $extras->encryptQuery1(KEY_SALT, 'id', $data->id, 'invoice_view.php');
                    }
                    
                    //date_default_timezone_set('Atlantic/Bermuda');
                    $date = date("F d, Y", strtotime(str_replace('-','/', $data->created_at)));
                    $time = date("h:i:s A", strtotime(str_replace('-','/', $data->created_at)));                    
                               
                    

                    echo "<tr>";
                    //echo "<td>$ind</td>";
                    echo "<td>$data->user_name</td>";
                    echo "<td>$data->user_address</td>";
                    echo "<td>$data->user_email</td>";
                    echo "<td>$data->mobile</td>";
                    echo "<td>$data->property_name</td>";
                    echo "<td>$data->propery_address</td>";
                    echo "<td><a href='".$viewUrl."' target='_blank'>Click here to view</a></td>";
                    //echo "<td>".$data->currency.' '.$data->price."</td>";
                    echo "<td>".'$'.number_format($data->price,2)."</td>";
                    echo "<td>$date</td>";
                    echo "<td>$time</td>";
                    echo "<td><a class='btn  btn-xs' href='$invoiceUrl'><span class='glyphicon glyphicon-file' title='Invoice'></span></a></td>";

                    echo "<td>
                                    
                                    <a href='javascript:void(0);'  class='btn btn-xs' data-toggle='modal' data-target='#modal_$data->id'><span class='glyphicon glyphicon-remove'></span></a>
                                    
                                </td>";
                    echo "</tr>";

                    echo "<div class='modal fade' id='modal_$data->id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Deleting Reserved Property</h4>
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


</body>
</html>