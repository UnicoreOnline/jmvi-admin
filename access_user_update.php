<?php
require_once 'header.php';
$controller = new ControllerAuthentication();


$extras = new Extras();
$authentication_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
$user = $controller->getAccessUserByAuthenticationId($authentication_id);

if ($authentication_id != null) {
    if (isset($_POST['submit'])) {

        $itm = new Authentication();
        $itm->authentication_id = $user->authentication_id;
        $itm->name = trim(strip_tags($_POST['name']));
        $itm->username = $user->username;

        $pass = trim(strip_tags($_POST['password']));
        $password_confirm = trim(strip_tags($_POST['password_confirm']));
        $password_current = trim(strip_tags($_POST['password_current']));
        $itm->password = md5($pass);

        if (strlen($pass) < 8) {
            echo "<script >alert('Password field must be atleast 8 alphanumeric characters.');</script>";
        } else if ($user->password != md5($password_current)) {
            echo "<script >alert('Current password does not match.');</script>";
        } else if ($pass != $password_confirm) {
            echo "<script >alert('Password does not match.');</script>";
        } else {
            $controller->updateAccessUser($itm);
            echo "<script type='text/javascript'>location.href='admin_access.php';</script>";
        }
    }
} else {
    echo "<script type='text/javascript'>location.href='403.php';</script>";
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

        <title>JMVI Real Estate</title>

        <!-- Bootstrap core CSS -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="bootstrap/css/navbar-fixed-top.css" rel="stylesheet">
        <link href="bootstrap/css/custom.css" rel="stylesheet">
        <script type="text/javascript">

            function validateField(evt) {
                var theEvent = evt || window.event;
                var key = theEvent.keyCode || theEvent.which;
                key = String.fromCharCode(key);


                if (theEvent.keyCode == 8 || theEvent.keyCode == 127 || theEvent.keyCode == 9) {

                }
                else {
                    var regex = /^([a-z0-9]+-)*[a-z0-9]+$/i;
                    if (!regex.test(key)) {
                        theEvent.returnValue = false;
                        if (theEvent.preventDefault)
                            theEvent.preventDefault();
                    }
                }
            }
        </script>

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

            <!-- Example row of columns -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Update Access User</h3>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <form action="" method="POST">
                                <div class="form-group row">
                                    <label for="password_current" class="col-sm-2 col-form-label">Current Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" placeholder="Current Password" name="password_current" onkeypress='validateField(event)' required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" placeholder="Password" name="password" onkeypress='validateField(event)' required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Confirm Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirm" onkeypress='validateField(event)' required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Full Name" name="name" required value="<?php echo $user->name; ?>">
                                    </div>
                                </div>                                
                                <p>
                                    <button type="submit" name="submit" class="btn btn-info"  role="button">Save</button> 
                                    <a class="btn btn-info" href="admin_access.php" role="button">Cancel</a>
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



    </body></html>