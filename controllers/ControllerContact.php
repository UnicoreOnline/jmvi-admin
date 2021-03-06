<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerContact
{
    private $db;
    private $pdo;

    function __construct()
    {
        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();
    }

    function __destruct()
    {
    }

   public function submitContact($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_contact( 
                                        name,
                                        email, 
                                        subject, 
                                        message, 
                                        created_at ) 
                                    VALUES( 
                                        :name,
                                        :email,
                                        :subject,
                                        :message,
                                        NOW() )');

        $result = $stmt->execute(
            array('name' => $itm->name,
                'email' => $itm->email,
                'subject' => $itm->subject,                
                'message' => $itm->message,
                ));

        return $result ? true : false;
    }
}