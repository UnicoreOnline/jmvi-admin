<?php

require_once 'header.php';
$controller = new ControllerCountry();
$extras = new Extras();
if (isset($_POST['submit'])) {

    $itm = new Country();
    $itm->country_name = trim(strip_tags($_POST['country_name']));    
    

    $controller->insertCountry($itm);
    echo "<script type='text/javascript'>location.href='country.php';</script>";

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

    <title>Country JMVI Real Estate</title>

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
            <h3 class="panel-title">Add Country</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-7">
                    <form action="" method="POST">                        
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" class="form-control" placeholder="Country Name" name="country_name"
                                   required>
                        </div>

                        <br/>                        
                        <p>
                            <button type="submit" name="submit" class="btn btn-info"
                                    role="button">Save
                            </button>
                            <a class="btn btn-info" href="country.php" role="button">Cancel</a>
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

</body>
</html>