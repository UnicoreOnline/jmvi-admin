<?php
require_once 'header.php';
$controller = new ControllerAgent();
$controllerUser = new ControllerUser();

$users = $controllerUser->getUsers();

$extras = new Extras();
$agent_id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);

if ($agent_id != null) {
    $agent = $controller->getAgentByAgentId($agent_id);
    
    if (isset($_POST['submit'])) {

        $itm = new Agent();
        $itm->address = trim(strip_tags($_POST['address']));
        $itm->contact_no = trim(strip_tags($_POST['contact_no']));
        $itm->country = trim(strip_tags($_POST['country']));
        $itm->created_at = time();
        $itm->email = trim(strip_tags($_POST['email']));
        $itm->name = trim(strip_tags($_POST['name']));
        $itm->sms = trim(strip_tags($_POST['sms']));
        $itm->updated_at = time();
        @$itm->zipcode = trim(strip_tags($_POST['zipcode']));
        $itm->photo_url = trim(strip_tags($_POST['photo_url']));
        $itm->thumb_url = trim(strip_tags($_POST['thumb_url']));

        $itm->twitter = trim(strip_tags($_POST['twitter']));
        $itm->fb = trim(strip_tags($_POST['fb']));
        $itm->linkedin = trim(strip_tags($_POST['linkedin']));
        $itm->company = trim(strip_tags($_POST['company']));
        //$itm->user_id = trim(strip_tags($_POST['user_id']));
        $itm->agent_id = $agent_id;
        $itm->website = trim(strip_tags($_POST['website']));
        $count = count($_FILES["file"]["name"]);

        if (!empty($_FILES["file"]["name"][0]) || !empty($_FILES["file"]["name"][1])) {
            uploadFile($controller, $itm);
        } else {

            $controller->updateAgent($itm);
            echo "<script type='text/javascript'>location.href='agents.php';</script>";
        }
    }
} else {
    echo "<script type='text/javascript'>location.href='403.php';</script>";
}

function uploadFile($controller, $itm) {

    $extras = new Extras();

    $desired_dir = Constants::IMAGE_UPLOAD_DIR;
    $errors = array();
    $count = count($_FILES["file"]["name"]);
    
    for ($key = 0; $key < $count; $key++) {

        $file_name = $_FILES['file']['name'][$key];
        $file_size = $_FILES['file']['size'][$key];
        $file_tmp = $_FILES['file']['tmp_name'][$key];
        $file_type = $_FILES['file']['type'][$key];

        if ($file_size > 2097152) {
            $errors[] = 'File size must be less than 2 MB';
        }
        if($file_size > 0){

            $date = date_create();
            $timestamp = time();
            $temp = explode(".", $_FILES["file"]["name"][0]);
            $extension = end($temp);


            $new_file_name = $desired_dir . "/" . "large_" . $timestamp . "." . $extension;

            if ($key == 1)
                $new_file_name = $desired_dir . "/" . "thumb_" . $timestamp . "." . $extension;

            if (empty($errors) == true) {
                if (is_dir($desired_dir) == false) {
                    // Create directory if it does not exist
                    mkdir("$desired_dir", 0700);
                }
                if (is_dir($file_name) == false) {
                    // rename the file if another one exist
                    move_uploaded_file($file_tmp, $new_file_name);
                } else {
                    $new_dir = $new_file_name . time();
                    rename($file_tmp, $new_dir);
                }

                if ($key == 0) {
                    $itm->photo_url = Constants::ROOT_URL . $new_file_name;
                }

                if ($key == 1) {
                    $itm->thumb_url = Constants::ROOT_URL . $new_file_name;
                }
            } else {
                print_r($errors);
            }
        }
    }

    $controller->updateAgent($itm);
    echo "<script type='text/javascript'>location.href='agents.php';</script>";
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
        <link href="bootstrap/css/fileinput.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                    <h3 class="panel-title">Update Agent</h3>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Agent Full Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Agent Name" name="name" required value="<?php echo $agent->name; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Company Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Company" name="company" required value="<?php echo $agent->company; ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Company Address</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" placeholder="Address" name="address" id="address" required><?php echo $agent->address; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Contact Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Contact No" name="contact_no" required value="<?php echo $agent->contact_no; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Email" name="email" required value="<?php echo $agent->email; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Whatsapp Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="SMS No" name="sms" required value="<?php echo $agent->sms; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Country</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Country" name="country" required value="<?php echo $agent->country; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Twitter</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Twitter" name="twitter" required value="<?php echo $agent->twitter; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Facebook</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Facebook" name="fb" required value="<?php echo $agent->fb; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Linked In</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Linked In" name="linkedin" required value="<?php echo $agent->linkedin; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Website</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Website" name="website" value="<?php echo $agent->website; ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Agent Profile Photo</label>                                    
                                    <div class="col-sm-10">
                                        <div class="main-image-preview_product">                                            
                                            <div class="col-sm-6 row">
                                                <div class="input-group">
                                                    <p>Photo File</p>     
                                                    <input type="hidden" name="photo_url" value="<?= $agent->photo_url ?>">
                                                    <div class="thumb_img_div" style="display: inline-block;">
                                                        <img src="<?= $agent->photo_url ?>" class="thumb_img" width="100px;"/>  
                                                    </div>
                                                    <input type="file" name="file[0]" class="file_upload_input" style="display: none;"/>                                                    
                                                    <a href="javascript:void(0);" class="file_upload">
                                                        <div class="col-sm-4 row file_add_box">
                                                            <div class="file-input theme-fa file-input-ajax-new">
                                                                <div class="file-preview ">                                                                 
                                                                    <div class="file-drop-zone clearfix clickable" tabindex="-1" style="border: 2px dashed #999;">
                                                                        <div class="upload_content">
                                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                            
                                                                <div class="clearfix"></div>                                                            
                                                            </div>
                                                        </div>
                                                    </a>                                                                                                        
                                                </div>
                                            </div>
                                            <div class="col-sm-6 row">
                                                <div class="input-group">
                                                    <p>Thumbnail File</p>    
                                                    <input type="hidden" name="thumb_url" value="<?= $agent->thumb_url ?>">
                                                    <div class="thumb_img_div" style="display: inline-block;">
                                                        <img src="<?= $agent->thumb_url ?>" class="thumb_img" width="100px;"/>  
                                                    </div>
                                                    <input type="file" name="file[1]" class="file_upload_input" style="display: none;"/>
                                                    <a href="javascript:void(0);" class="file_upload">
                                                        <div class="col-sm-4 row file_add_box">
                                                            <div class="file-input theme-fa file-input-ajax-new">
                                                                <div class="file-preview ">                                                                 
                                                                    <div class="file-drop-zone clearfix clickable" tabindex="-1" style="border: 2px dashed #999;">
                                                                        <div class="upload_content">
                                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                            
                                                                <div class="clearfix"></div>                                                            
                                                            </div>
                                                        </div>
                                                    </a>                                                     
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>                  





                            </div>
                            <p>
                                <button type="submit" name="submit" class="btn btn-info"  role="button">Save</button> 
                                <a class="btn btn-info" href="agents.php" role="button">Cancel</a>
                            </p>
                        </div>
                </form><!--/.form -->
            </div>


        </div> <!-- /container -->


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="bootstrap/js/jquery.js"></script>
        <script src="bootstrap/js/bootstrap.js"></script>
        <script>
            $(document).on('click', '.file_upload', function () {
                $(this).prev('.file_upload_input').trigger("click");
            });
            $('.file_upload_input').change(function(){
                readURL(this,$(this));
            });
            function readURL(input, input_ele) {
                console.log(input.files[0]);
                if (input.files && input.files[0])
                {
                    var reader = new FileReader();
                    reader.onload = function (e)
                    {
                        input_ele.prev('.thumb_img_div').find('.thumb_img').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>


    </body></html>