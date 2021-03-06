<?php

class ControllerRestSeller
{
 
    private $db;
    private $pdo;
    function __construct($db_path) 
    {
        require_once $db_path;

        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();
    }

    function setClassPath($class_path) {
        require_once $class_path;
    }
 
    function __destruct() { }
 
    public function updateSeller($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_carfinder_sellers 
                                        SET 
                                            seller_name = :seller_name, 
                                            contact_no = :contact_no, 
                                            email = :email, 
                                            lat = :lat, 
                                            lon = :lon, 
                                            sms = :sms, 
                                            address = :address, 
                                            user_id = :user_id 

                                        WHERE seller_id = :seller_id');

        $result = $stmt->execute(
                            array('seller_name' => $itm->seller_name,
                                    'contact_no' => $itm->contact_no,
                                    'email' => $itm->email,
                                    'lat' => $itm->lat,
                                    'lon' => $itm->lon,
                                    'sms' => $itm->sms,
                                    'address' => $itm->address,
                                    'user_id' => $itm->user_id,
                                    'seller_id' => $itm->seller_id) );
        
        return $result ? true : false;
    }

    public function insertSeller($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_carfinder_sellers( 
                                        seller_name, 
                                        contact_no, 
                                        email, 
                                        lat, 
                                        lon, 
                                        sms, 
                                        address,
                                        user_id ) 

                                    VALUES(
                                        :seller_name, 
                                        :contact_no, 
                                        :email, 
                                        :lat, 
                                        :lon, 
                                        :sms, 
                                        :address,
                                        :user_id )');

        $result = $stmt->execute(
                            array('seller_name' => $itm->seller_name,
                                    'contact_no' => $itm->contact_no,
                                    'email' => $itm->email,
                                    'lat' => $itm->lat,
                                    'lon' => $itm->lon,
                                    'sms' => $itm->sms,
                                    'address' => $itm->address,
                                    'user_id' => $itm->user_id) );
        
        return $result ? true : false;

    }

    public function getUserSellerIfExist($user_id) 
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_carfinder_sellers 
                                        WHERE user_id = :user_id");

        $stmt->execute( array('user_id' => $user_id));
        foreach ($stmt as $row) 
        {
            $itm = new Seller();
            $itm->seller_id = $row['seller_id'];
            $itm->seller_name = $row['seller_name'];
            $itm->contact_no = $row['contact_no'];
            $itm->email = $row['email'];
            $itm->lat = $row['lat'];
            $itm->lon = $row['lon'];
            $itm->sms = $row['sms'];
            $itm->address = $row['address'];
            $itm->user_id = $row['user_id'];
            $itm->profile_pic = $row['profile_pic'];

            return $itm;
        }

        return null;
    }
 
}
 
?>