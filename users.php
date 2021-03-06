<?php

require_once 'models/User.php';
require_once 'header.php';
$controller = new ControllerUser('application/DB_Connect.php');
$users = $controller->getUsers();

if (!empty($_SERVER['QUERY_STRING'])) {

    $extras = new Extras();
    $params = $extras->decryptQuery2(KEY_SALT, $_SERVER['QUERY_STRING']);
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    

    $user_id = $params[0];
    $deny_access = $params[1] == 0 ? 1 : 0;

    if ($params != null) {
        $controller->updateUserAccess($user_id, $deny_access);
        echo json_encode(['status'=>'200','message'=>'Success']);
        exit;
        //echo "<script type='text/javascript'>location.href='users.php';</script>";
    } else {
		if (isset($id)) {
			$controller->deleteUser($id, 1);
			echo "<script type='text/javascript'>location.href='users.php';</script>";
		}
        echo json_encode(['status'=>'500','message'=>'Fail']);
        exit;
        //echo "<script type='text/javascript'>location.href='403.php';</script>";
    }

}


$search_criteria = "";
if (isset($_POST['button_search'])) {
    $search_criteria = trim(strip_tags($_POST['search']));
    $users = $controller->getUsersBySearching($search_criteria);
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

    <title>Users JMVI Real Estate</title>

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
            <h4 class="panel-title pull-left" style="padding-top: 7px;">Users</h4>
            <div class="btn-group pull-right">
                <!-- <a href="seller_insert.php" class="btn btn-default btn-sm">Add Seller</a> -->
                <form method="POST" action="">
                    <input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search"
                           name="search" value="<?php echo $search_criteria; ?>">
                    <button type="submit" name="button_search" class="btn btn-default btn-sm"><span
                                class="glyphicon glyphicon-search"></span></button>
                    <button type="submit" class="btn btn-default btn-sm" name="reset"><span
                                class="glyphicon glyphicon-refresh"></span></button>
                    <a href="user_add.php" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-plus'></span></a>

                </form>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Address</th>
                <th>Country</th>
                <th>Email Address</th> 
                <th>Contact Number</th>
                <th>Facebook</th>
                <th>Twitter</th>
                <th>Register Via</th>
                <th>Access</th>
                <th></th>

            </tr>

            </thead>
            <tbody>
            <?php

            if ($users != null) {

                $ind = 1;
                foreach ($users as $user) {

                    $extras = new Extras();
                    $featuredUrl = $extras->encryptQuery2(KEY_SALT, 'user_id', $user->user_id, 'user_id', $user->deny_access, 'users.php');
                    $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'id', $user->user_id, 'users.php');
					$updateUrl = $extras->encryptQuery1(KEY_SALT, 'user_id', $user->user_id, 'user_update.php');

                    echo "<tr>";
                    echo "<td>$user->user_id</td>";
                    echo "<td>$user->full_name</td>";
                    echo "<td>$user->address</td>";
                    echo "<td>$user->country</td>";
                    echo "<td>$user->email</td>";
                    echo "<td>$user->mobile</td>";
                    
                    echo "<td>$user->facebook_url</td>";
                    echo "<td>$user->twitter_url</td>";
                    
                    
                    
                    

                    $registered_via = @"Web";

                    if ($user->facebook_id > 0)
                        $registered_via = @"Facebook";

                    if ($user->twitter_id > 0)
                        $registered_via = @"Twitter";

                    echo "<td>$registered_via</td>";

                    echo "<td><div class='button b2 switch_button' id='button-10'>
                                <input type='checkbox' ".($user->deny_access != 1 ? 'checked':'')." class='checkbox user_access' data-url='".$featuredUrl."'>
                                <div class='knobs'>
                                        <span>On</span>
                                </div>
                                <div class='layer'></div>
                        </div></td>";
                    if ($user->deny_access == 1) {
                        //echo "<td><a href='$featuredUrl'>Allow</a></td>";
                    } else {
                       // echo "<td><a href='$featuredUrl'>Deny</a></td>";
                    }

                    echo "<td>
							<a class='btn  btn-xs' href=\"$updateUrl\"><span class='glyphicon glyphicon-pencil'></span></a>
							<a href='javascript:void(0);'  class='btn  btn-xs' data-toggle='modal' data-target='#modal_$user->user_id'><span class='glyphicon glyphicon-remove'></span></a>
						</td>";
                    echo "</tr>";

                    echo "<div class='modal fade' id='modal_$user->user_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

                                      <div class='modal-dialog'>
                                          <div class='modal-content'>
                                              <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                                    <h4 class='modal-title' id='myModalLabel'>Deleting User</h4>
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
<script>
    $( document ).ready(function() {
        $(".user_access").change(function () {
            var accessUrl = $(this).attr('data-url');
            $.ajax({
                type: "GET",
                url: accessUrl,
                async: true,
                data: {
                    //action1: value // as you are getting in php $_POST['action1'] 
                },
                success: function (msg) {
                    var msg = JSON.parse(msg);
                    
                    if(msg.status == '200'){
                        alert('Access Provided Successfully.');
                        //window.location.href='users.php';
                    } else {
                        alert('Something went wrong.');
                    }
                }
            });
        });
    });    
</script>

</body>
</html>