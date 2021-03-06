<?php
require_once 'header.php';
$controller = new ControllerBanner();


$extras = new Extras();
if (isset($_POST) && !empty($_POST)) {
    
    $itm = new Banner();
    $desired_dir = Constants::IMAGE_UPLOAD_DIR;
    if (isset($_FILES['photo']) && !empty($_FILES['photo']) && isset($_FILES['photo']['name'])) {
        $file_name = $_FILES['photo']['name'];
        $file_size = $_FILES['photo']['size'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_type = $_FILES['photo']['type'];

        if(!empty($file_name)){
            $timestamp = time();
            $temp = explode(".", $file_name);
            $extension = end($temp);

            $new_file_name = "banner_" . $timestamp . "." . $extension;

            if (is_dir($desired_dir) == false) {
                // Create directory if it does not exist
                mkdir("$desired_dir", 0700);
            }

            move_uploaded_file($file_tmp, $desired_dir . "/banner/" . $new_file_name);
            $itm->banner_name = $new_file_name;
            $itm->link = trim(strip_tags($_POST['link']));
        }
    }

    $controller->insertBanner($itm);

    header('Location: banner_list.php');  
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
        <div class="panel-heading">
            <h3 class="panel-title">Add Banner</h3>
        </div>

        <div class="panel-body">
            <div class="row">

                <div class="col-md-6">

                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="input-group">
                            <p>Banner File</p>
                            <input type="file" name="photo"/>
                        </div>
                        <br/>
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" class="form-control" placeholder="Banner Link" name="link"
                                   required>
                        </div>
                        <br/>
                        <p>
                            <button type="submit" name="file_upload" class="btn btn-info" role="button">Save</button>
                            <?php                            
                            echo "<a class='btn btn-info' href='banner_list.php' role='button'>Cancel</a>";
                            ?>
                        </p>

                    </form><!--/.form -->

                </div><!--/.col-md-6 -->

            </div><!--/.row -->
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