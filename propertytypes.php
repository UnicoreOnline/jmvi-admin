<?php 

  require_once 'header.php';
  $controller = new ControllerPropertyType();
  $propertytypes = $controller->getPropertyTypes();

  if(!empty($_SERVER['QUERY_STRING'])) {

      $extras = new Extras();
      $propertytype_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
      if( $propertytype_id != null ) {
          $controller->deletePropertyType($propertytype_id, 1);
          echo "<script type='text/javascript'>location.href='propertytypes.php';</script>";
      }
      else {
        echo "<script type='text/javascript'>location.href='403.php';</script>";
      }
  }

  $search_criteria = "";
  if( isset($_POST['button_search']) ) {
      $search_criteria = trim(strip_tags($_POST['search']));
      $propertytypes = $controller->getPropertyTypesBySearching($search_criteria);
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

    <title>Property Type JMVI Real Estate</title>

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
          <h4 class="panel-title pull-left" style="padding-top: 7px;">Property Types</h4>
          <div class="btn-group pull-right">
            <!-- <a href="make_insert.php" class="btn btn-default btn-sm">Add Make</a> -->
            <form method="POST" action="">
                  <input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search" name="search" value="<?php echo $search_criteria; ?>">
                  <button type="submit" name="button_search" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-search"></span></button>
                  <button type="submit" class="btn btn-default btn-sm" name="reset"><span class="glyphicon glyphicon-refresh"></span></button>
                  <a href="propertytype_insert.php" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-plus'></span></a>
            </form>
          </div>
        </div>

        <!-- Table -->
        <table class="table">
          <thead>
              <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Action</th>
              </tr>

          </thead>
          <tbody>
              <?php 

                  if($propertytypes != null) {

                    $ind = 1;
                    foreach ($propertytypes as $propertytype)  {

                          $extras = new Extras();
                          $updateUrl = $extras->encryptQuery1(KEY_SALT, 'propertytype_id', $propertytype->propertytype_id, 'propertytype_update.php');
                          $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'propertytype_id', $propertytype->propertytype_id, 'propertytypes.php');

                          echo "<tr>";
                          echo "<td>$ind</td>";
                          echo "<td>$propertytype->property_type</td>";
                          echo "<td>
                                    <a class='btn  btn-xs' href='$updateUrl'><span class='glyphicon glyphicon-pencil'></span></a>
                                    <a href='javascript:void(0);' class='btn  btn-xs' data-toggle='modal' data-target='#modal_$propertytype->propertytype_id'><span class='glyphicon glyphicon-remove'></span></a>
                                </td>";
                          echo "</tr>";

                          echo "<div class='modal fade' id='modal_$propertytype->propertytype_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Deleting Property Type</h4>
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
    
  

</body></html>