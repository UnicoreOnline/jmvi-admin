<?php 

  require_once 'header.php';
  $controller = new ControllerReservedProperty();

  $limit = 10;
  $page = isset($_GET['page']) ? $_GET['page'] : 1;
  
  $search_criteria = "";
  if( isset($_POST['button_search']) ) {
      $search_criteria = trim(strip_tags($_POST['search']));
  }
  
  
  $return = $controller->getGroupedReservedProperty($page , $search_criteria);

  $properties = isset($return['records']) ? $return['records'] : null;
  $_SESSION['properties'] = serialize($properties);
  $total = isset($return['total']) ? $return['total'] : 0;

  if (!empty($_SERVER['QUERY_STRING'])) {

    $extras = new Extras();
    $params = $extras->decryptQuery3(KEY_SALT, $_SERVER['QUERY_STRING']);
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    
    $propertyId = $params[0];
    $userId = $params[1];
    $isAllowed = $params[2] == 0 ? 1 : 0;

    if ($params != null) {
        $controller->updateRegisteredBidder($propertyId, $userId, $isAllowed);
        echo json_encode(['status'=>'200','message'=>'Success']);
        exit;        
    }

    if (isset($_GET['del_id'])) {
          $controller->deleteReservedProperty($_GET['del_id'], 1);
          echo "<script type='text/javascript'>location.href='registered_bidders.php';</script>";
      
    }

  }

  

?>


<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="bootstrap/images/16px_JMVI_logo.png">

    <title>Registered Bidders JMVI Real Estate</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="bootstrap/css/navbar-fixed-top.css" rel="stylesheet">
    <link href="bootstrap/css/custom.css" rel="stylesheet">


    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

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
          <h4 class="panel-title pull-left" style="padding-top: 7px;">Registered Bidders</h4>
          <div class="btn-group pull-right">
            <form method="POST" action="">
				<input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search" name="search" value="<?php echo $search_criteria; ?>">
				<button type="submit" name="button_search" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-search"></span></button>
				<button type="submit" class="btn btn-default btn-sm" name="reset"><span class="glyphicon glyphicon-refresh"></span></button>
        <?php
            $extras = new Extras();
            $excelUrl = $extras->encryptQuery1(KEY_SALT, 'ref_id', 5, 'download_excel.php');
        ?>
        <a href="<?= $excelUrl ?>"  class="btn btn-default btn-sm"><span
                    class='glyphicon glyphicon-download-alt'></span></a>
			</form>
          </div>
        </div>

        <!-- Table -->
        <table class="table">
          <thead>
              <tr>                  
                  <th>User Name</th>
                  <th>Contact Number</th>
                  <th>Email Address</th>
                  <th>User Address</th>                  
                  <th>Property Name</th>
                  <th>Created Date</th>
                  <th>Allow/Deny</th>
                  <th></th>
              </tr>

          </thead>
          <tbody>
              <?php 

                  if($properties != null) {

                    foreach ($properties as $property)  {

                          $accessRoute = $extras->encryptQuery3(KEY_SALT, 'id', $property->property_id, 'id', $property->user_id, 'id', $property->is_allowed, 'registered_bidders.php');
                          echo "<tr>";
                          echo "<td>$property->user_name</td>";
                          echo "<td>$property->mobile</td>";
                          echo "<td>$property->user_email</td>";
                          echo "<td>$property->user_address</td>";
                          echo "<td>$property->property_name</td>";
                          echo "<td>$property->created_at</td>";
                          echo "<td><div class='button b2 switch_button' id='button-10'>
                                <input type='checkbox' ".($property->is_allowed == 1 ? 'checked':'')." class='checkbox allow_user' data-url='".$accessRoute."'>
                                <div class='knobs'>
                                        <span>On</span>
                                </div>
                                <div class='layer'></div>
                        </div></td>";    
                          echo "<td>
                                    <a href='javascript:void(0);' class='btn  btn-xs' data-toggle='modal' data-target='#modal_$property->id'><span class='glyphicon glyphicon-remove'></span></a>
                                </td>";
                          
                          echo "</tr>";
                          echo "<div class='modal fade' id='modal_$property->id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Deleting Registered Bidder</h4>
                                              </div>
                                              <div class='modal-body'>
                                                    <p>Deleting this is not irreversible. Do you wish to continue?
                                              </div>
                                              <div class='modal-footer'>
                                                  <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                  <a type='button' class='btn btn-primary' href='registered_bidders.php?del_id=$property->id'>Delete</a>
                                              </div>
                                          </div>
                                      </div>
                                </div>";
                    }
                  }
              ?>

          </tbody>
        </table>
      </div>

      <?php
      if ($total > 0) {
        $totalPages = ceil($total/$limit);

        if ($page > 1) {
          echo '<a href="registered_bidders.php?page='.($page-1).'" class="btn btn-primary">Previous Page</a>';
        }

        if ($page != $totalPages && $totalPages != 0) {
          echo '&nbsp;&nbsp;&nbsp;<a href="registered_bidders.php?page='.($page+1).'" class="btn btn-primary">Next Page</a>';
        }
      }
      ?>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
    
    <script>
        $( document ).ready(function() {
            $(".allow_user").change(function () {
                var accessUrl = $(this).attr('data-url');
                $.ajax({
                    type: "GET",
                    url: accessUrl,
                    async: true,
                    data: {
                       
                    },
                    success: function (msg) {
                        var msg = JSON.parse(msg);
                        
                        if(msg.status == '200'){
                            alert('User Updated Successfully.');
                            //window.location.href='users.php';
                        } else {
                            alert('Something went wrong.');
                        }
                    }
                });
            });
        });    
        
    </script>
  

</body></html>