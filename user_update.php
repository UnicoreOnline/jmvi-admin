<?php
require_once 'header.php';

$extras = new Extras();
$user_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);

if ($user_id != null) {

    $controller = new ControllerUser();
    $user = $controller->getUserByUserId($user_id);

    if (isset($_POST['submit'])) {
        $itm = new User();
        $itm->username = trim(strip_tags($_POST['username']));
        $itm->full_name = trim(strip_tags($_POST['full_name']));
        $itm->country = trim(strip_tags($_POST['country']));
        $itm->address = trim(strip_tags($_POST['address']));
        $itm->mobile = trim(strip_tags($_POST['mobile']));
        $itm->facebook_url = $user->facebook_url;
        $itm->twitter_url = $user->twitter_url;
        
        if(isset($_POST['facebook_url']) && !empty($_POST['facebook_url'])){
            $itm->facebook_url = trim(strip_tags($_POST['facebook_url']));            
        }
        if(isset($_POST['twitter_url']) && !empty($_POST['twitter_url'])){            
            $itm->twitter_url = trim(strip_tags($_POST['twitter_url']));
        }
        $itm->user_id = $user_id;
        
        if (!empty(trim(strip_tags($_POST['password'])))) {
            $itm->password = md5(trim(strip_tags($_POST['password'])));
        } else {
            $itm->password = $user->password;
        }

        $controller->updateUser($itm);
        echo "<script type='text/javascript'>location.href='users.php';</script>";
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
                    <h3 class="panel-title">Update User</h3>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label for="full_name" class="col-sm-2 col-form-label">Full Name</label>
                                    <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Name" name="full_name" id="full_name" required value="<?php echo $user->full_name; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" name="address" id="address"><?php echo $user->address; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="country" class="col-sm-2 col-form-label">Country</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Country" name="country" id="country" value="<?php echo $user->country; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" disabled="disabled" class="form-control" placeholder="Email" name="email" id="email" required value="<?php echo $user->email; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">Contact Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" value="<?php echo $user->mobile; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label">Facebook</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Facebook" name="facebook_url" id="username" value="<?php echo $user->facebook_url; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label">Twitter</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Twitter" name="twitter_url" id="username" value="<?php echo $user->twitter_url; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Username" name="username" id="username" required value="<?php echo $user->username; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Password" name="password" id="password" value="">
                                        <p style="color:red;">Please leave it blank if you don't want to change password</p>
                                    </div>
                                </div>
                                <p>
                                    <button type="submit" name="submit" class="btn btn-info"  role="button">Save</button> 
                                    <a class="btn btn-info" href="users.php" role="button">Cancel</a>
                                </p>
                                <br />
                            </div>
                        </div>
                </form><!--/.form -->
            </div>


        </div> <!-- /container -->


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="bootstrap/js/jquery.js"></script>
        <script src="bootstrap/js/bootstrap.js"></script>



    </body></html>