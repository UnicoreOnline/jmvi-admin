<?php
    require_once 'header.php';
    $controllerPhoto = new ControllerPhoto();
    if(isset($_POST) && isset($_POST['key']) && !empty($_POST['key'])){
        $photo_id = $_POST['key'];
        $controllerPhoto->deletePhoto($photo_id, 1);
    }
    
   echo json_encode(['message'=>'Deleted file successfully!']);
?>