<?php 

  require_once 'header.php';
  $controller = new ControllerRealEstate();
  $realestates = $controller->getRealEstateFeatured();

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

    <title>JMVI Real Estate</title>

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
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">


        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">JMVI Real Estate</a>
        </div>


        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="home.php">Home</a></li>
            <li ><a href="propertytypes.php">Property Type</a></li>
            <li><a href="realestates.php">Real Estates</a></li>
            <li><a href="agents.php">Agents</a></li>
            <li><a href="auction.php">Auction</a></li>
            <li><a href="admin_access.php">Admin Access</a></li>
            <li ><a href="users.php">Users</a></li>
            <li><a href="reservedproperty.php">Reserved Property</a></li>
              <li><a href="bid_made.php">Bid Made</a></li>

          </ul>
          
          <ul class="nav navbar-nav navbar-right">
            <li ><a href="index.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
        
      </div>
    </div>

    <div class="container">

      <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top:7px;padding-bottom:6px;">Featured Real Estates</h4>
                <div class="btn-group pull-right">
                  
                </div>
              </div>

              <!-- Table -->
              <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Address</th>
                    </tr>

                </thead>
                <tbody>
                    <?php 

                        if($realestates != null) {

                          $ind = 1;
                          foreach ($realestates as $realestate)  {
                                echo "<tr>";
                                echo "<td>$ind</td>";
                                echo "<td>$realestate->price</td>";
                                echo "<td>$realestate->address</td>";
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
    
  

</body></html>