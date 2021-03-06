<?php 
  
  require 'header.php';
  $controller = new ControllerAuthentication();

  $users = $controller->getAccessUser();
  
  function isAdminUser()
  {
	  return strtolower($_SESSION["name"]) == "admin";
  }	  

  if(!empty($_SERVER['QUERY_STRING'])) {

      $extras = new Extras();
      $params = $extras->decryptQuery2(KEY_SALT, $_SERVER['QUERY_STRING']);

      $user_id = $params[0];
      $deny_access = $params[1] == 0 ? 1 : 0;

      if( $params != null && $params[1] == "deleted") {
        if(isAdminUser() == false && $user_id == 1) {
			$_SESSION['error'] = "Sorry, you are not allowed to delete admin user";
		}else{
			$controller->deleteAccessUser($user_id, 1);
		}	
        echo "<script type='text/javascript'>location.href='admin_access.php';</script>";
      }
      else if( $params != null && $deny_access >= 0) {
        if(isAdminUser() == false && $user_id == 1) {
		    $_SESSION['error'] = "Sorry, you are not allowed to deny admin access";
		}else{
			$controller->denyUserAccess($user_id, $deny_access);
        }  
		echo "<script type='text/javascript'>location.href='admin_access.php';</script>";
      }
      else {
        echo "<script type='text/javascript'>location.href='403.php';</script>";
      }
  }
  

  $search_criteria = "";
  if( isset($_POST['button_search']) ) {
      $search_criteria = trim(strip_tags($_POST['search']));
      $users = $controller->getAccessUsersBySearching($search_criteria);
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

    <title>Admin Access JMVI Real Estate</title>

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
          <h4 class="panel-title pull-left" style="padding-top: 7px;">Access Users</h4>
          <div class="btn-group pull-right">
            <!-- <a href="seller_insert.php" class="btn btn-default btn-sm">Add Seller</a> -->
            <form method="POST" action="">
                  <input type="text" style="height:100%;color:#000000;padding-left:5px;" placeholder="Search" name="search" value="<?php echo $search_criteria; ?>">
                  <button type="submit" name="button_search" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-search"></span></button>
                  <button type="submit" class="btn btn-default btn-sm" name="reset"><span class="glyphicon glyphicon-refresh"></span></button>
                  <a href="access_user_insert.php" class="btn btn-default btn-sm"><span class='glyphicon glyphicon-plus'></span></a>
            </form>
          </div>
        </div>

        <!-- Table -->
        <table class="table">
          <thead>
              <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Access Role</th>
                  <th>Action</th>
              </tr>

          </thead>
          <tbody>
              <?php 

                  if($users != null) {

                    $ind = 1;
                    foreach ($users as $user)  {

                          $extras = new Extras();
                          $featuredUrl = $extras->encryptQuery2(KEY_SALT, 'authentication_id', $user->authentication_id, 'authentication_id', $user->deny_access, 'admin_access.php');
                          $updateUrl = $extras->encryptQuery1(KEY_SALT, 'authentication_id', $user->authentication_id, 'access_user_update.php');
                          $deleteUrl = $extras->encryptQuery1(KEY_SALT, 'authentication_id', $user->authentication_id, 'admin_access.php');
                          $deleteUrl = $extras->encryptQuery2(KEY_SALT, 'authentication_id', $user->authentication_id, 'deleted', "deleted", 'admin_access.php');

                          echo "<tr>";
                          echo "<td>$ind</td>";
                          echo "<td>$user->name</td>";
                          echo "<td>$user->username</td>";

						  if($user->deny_access == 1) {
                            $accessBtn = "<td><a href='$featuredUrl'>Allow</a></td>";
                          }else {
                            $accessBtn = "<td><a href='$featuredUrl'>Deny</a></td>";
                          }
						  if(isAdminUser() == false && $user->authentication_id == 1){
							$accessBtn = "<td></td>";
						  }
						  
						  echo $accessBtn;
						  
						  $editButton = "<a class='btn  btn-xs' href='$updateUrl'><span class='glyphicon glyphicon-pencil'></span></a>
						  <a href='javascript:void(0);'  class='btn  btn-xs' data-toggle='modal' data-target='#modal_$user->authentication_id'><span class='glyphicon glyphicon-remove'></span></a>";
						  if(isAdminUser() == false && $user->authentication_id == 1) {
							$editButton = "";
						  }
                        
                          echo "<td>
									{$editButton}
                                </td>";
                          echo "</tr>";


                          //<!-- Modal -->
                          echo "<div class='modal fade' id='modal_$user->authentication_id' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>

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
    
  

</body></html>