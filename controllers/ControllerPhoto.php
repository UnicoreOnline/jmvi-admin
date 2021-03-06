<?php

class ControllerPhoto
{
 
    private $db;
    private $pdo;
    function __construct() 
    {
        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();
    }

    function setClassPath($class_path) {
        require_once $class_path;
    }
 
    function __destruct() { }
 
    public function updatePhoto($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_photos
                                        SET 
                                            photo_url = :photo_url, 
                                            thumb_url = :thumb_url, 
                                            realestate_id = :realestate_id,
                                            updated_at = :updated_at  

                                            WHERE photo_id = :photo_id');

        $result = $stmt->execute(
                            array('photo_url' => $itm->photo_url,
                                    'thumb_url' => $itm->thumb_url,
                                    'realestate_id' => $itm->realestate_id, 
                                    'updated_at' => $itm->updated_at, 
                                    'photo_id' => $itm->photo_id) );
        
        return $result ? true : false;
    }


    public function insertPhoto($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_realestate_photos( 
                                            photo_url, 
                                            thumb_url, 
                                            realestate_id,
                                            created_at,
                                            updated_at ) 

                                        VALUES(
                                            :photo_url, 
                                            :thumb_url, 
                                            :realestate_id,
                                            :created_at,
                                            :updated_at )');

        $result = $stmt->execute(
                            array('photo_url' => $itm->photo_url,
                                    'thumb_url' => $itm->thumb_url,
                                    'realestate_id' => $itm->realestate_id,
                                    'created_at' => $itm->created_at,
                                    'updated_at' => $itm->updated_at ) );
        
        return $result ? true : false;
    }
 
    public function deletePhoto($photo_id, $is_deleted) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_photos 
                                
                                        SET 
                                            is_deleted = :is_deleted 

                                        WHERE photo_id = :photo_id');
        
        $result = $stmt->execute(
                            array('photo_id' => $photo_id,
                                    'is_deleted' => $is_deleted ) );
        
        return $result ? true : false;
    }

    public function getPhotoByPhotoId($photo_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 

                                        FROM tbl_realestate_photos 
                                        WHERE photo_id = :photo_id');

        $result = $stmt->execute(
                            array('photo_id' => $photo_id) );

        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new Photo();
            $itm->photo_id = $row['photo_id'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];
            $itm->realestate_id = $row['realestate_id'];

            return $itm;
        }
        return null;
    }

    public function getLastInsertedPhotoId() {

        return $this->pdo->lastInsertId(); 
    }

    public function getNoOfPhotosByRealEstateId($realestate_id) 
    {
       $stmt = $this->pdo->prepare('SELECT * 

                                        FROM tbl_realestate_photos 
                                        WHERE realestate_id = :realestate_id AND is_deleted = 0');

        $result = $stmt->execute(
                            array('realestate_id' => $realestate_id) );

        $no_of_rows = $stmt->rowCount();

       return $no_of_rows;
    }

    public function getPhotosByRealEstateId($realestate_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 

                                        FROM tbl_realestate_photos 
                                        WHERE realestate_id = :realestate_id AND is_deleted = 0');

        $result = $stmt->execute(
                            array('realestate_id' => $realestate_id) );

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new Photo();
            $itm->photo_id = $row['photo_id'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->realestate_id = $row['realestate_id'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }
 
}
 
?>