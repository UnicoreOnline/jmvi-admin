<?php
require_once 'header.php';
$controller = new ControllerLawyer();

$rows = $controller->getLawyers();

if (!empty($_SERVER['QUERY_STRING'])) {

    $extras = new Extras();
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);

    if ($id != null) {
        $controller->deleteLawyer($id, 1);
        echo "<script type='text/javascript'>location.href='lawyers.php';</script>";
    }

}

$search_criteria = "";
if (isset($_POST['button_search'])) {
    $search_criteria = trim(strip_tags($_POST['search']));
    $rows = $controller->getLawyersBySearching($search_criteria);
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

    <title>JMVI Lawyers</title>

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

<div class="container">

    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Lawyers</h4>
            <div class="btn-group pull-right">
                <!-- <a href="car_insert.php" class="btn btn-default btn-sm">Add Car</a> -->
                <form method="POST" action="">
                    <input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search"
                           name="search" value="<?php echo $search_criteria; ?>">
                    <button type="submit" name="button_search" class="btn btn-default btn-sm"><span
                                class="glyphicon glyphicon-search"></span></button>
                    <button type="submit" class="btn btn-default btn-sm" name="reset"><span
                                class="glyphicon glyphicon-refresh"></span></button>
                    <a href="lawyer_insert.php" class="btn btn-default btn-sm"><span
                                class='glyphicon glyphicon-plus'></span></a>
                </form>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
            <tr>
                <th>Lawyer Full Name</th>
                <th>Company Name</th>
                <th>Company Address</th>
                <th>Website</th>
                <th>Contact Number</th>
                <th>Email Address</th>
                <th>Whatsapp Number</th>
                <th>Lawyer Profile Photo</th>
            </tr>

            </thead>
            <tbody>
            <?php

            if ($rows != null) {

                $ind = 1;
                foreach ($rows as $row) {
                    
                    $extras = new Extras();
                    $updateUrl = $extras->encryptQuery1(KEY_SALT, 'id', $row->lawyer_id, 'lawyer_update.php');
                    $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'id', $row->lawyer_id, 'lawyers.php');
                    $photo_Url = Constants::ROOT_URL.Constants::IMAGE_UPLOAD_DIR.'/'.$row->photo_url;
                    
                    echo "<tr>";
                    echo "<td>$row->name</td>";
                    echo "<td>$row->company_name</td>";
                    echo "<td>$row->address</td>";
                    echo "<td>$row->website</td>";
                    echo "<td>$row->contact_no</td>";
                    echo "<td>$row->email</td>";
                    echo "<td>$row->whatsapp_no</td>";
                    echo "<td>".($row->photo_url != '' ? "<a  data-toggle='modal' data-target='#modal_photo_$row->lawyer_id' href='javascript:void(0);'>Click here to view</a>" :"")."</td>";                    
                    echo "<td>
                                    <a class='btn  btn-xs' href='$updateUrl'><span class='glyphicon glyphicon-pencil'></span></a>
                                    <a href='javascript:void(0);' class='btn  btn-xs' data-toggle='modal' data-target='#modal_$row->lawyer_id'><span class='glyphicon glyphicon-remove'></span></a>
                                </td>";
                    echo "</tr>";

                    echo "<div class='modal fade' id='modal_$row->lawyer_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Deleting Lawyer</h4>
                                              </div>
                                              <div class='modal-body'>
                                                    <p>Deleting this is not irreversible. Do you wish to continue?
                                              </div>
                                              <div class='modal-footer'>
                                                  <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                  <a type='button' class='btn btn-info' href='$deleteUrl'>Delete</a>
                                              </div>
                                          </div>
                                      </div>
                                </div>";
                    
                    //<!-- Modal -->
                        echo "<div class='modal fade' id='modal_photo_$row->lawyer_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>";
                        echo "<h4 class='modal-title' id='myModalLabel'>Photo</h4>";
                        echo "</div>";

                        echo "<div class='modal-body'>";
                        echo "<img src='$photo_Url' style = 'display:block; height:100%; margin-left:auto; margin-right:auto; max-width:100%;'/>";
                        echo "</div>";

                        echo "</div>";
                        echo "</div>";
                        echo "</div>";


                        //<!-- Modal -->
                    

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