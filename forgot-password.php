<?php 
  
  session_start(); 
  $_SESSION['name'] = "";

  require 'controllers/ControllerAuthentication.php';
  
  
  if( isset($_POST['submit']) ) {
	  
	  $controller = new ControllerAuthentication();
      $res = $controller->resetPassword($_POST['username']);

      if($res) {
        echo "<script>alert('Your password has been reset and send email and you will be redirect to home page in 3 sec.');
			setTimeout(function(){ location.href = 'index.php' }, 3000);
			</script>";
      } else {
        echo "<script>alert('Invalid Username.');</script>";
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

    <title>JMVI Real Estate Reset Password</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="bootstrap/css/signin.css" rel="stylesheet">
    <link href="bootstrap/css/custom.css" rel="stylesheet">

  </head>

  <body>

    <div class="container">

      <form class="form-signin" role="form" method="POST">
        <img src="bootstrap/images/300px_JMVI_logo.png" class="center-block"/>
        <h3 class="form-signin-heading">Enter your username for reset password</h3>
        <input class="form-control" placeholder="Username" required="" autofocus="" type="text" name="username" required>
		</br>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Reset</button>
      </form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/bootstrap.js"></script>

  </body>
</html>