<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 13/4/18
 * Time: 9:59 PM
 */

class ControllerFavoriteProperty
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

    public function insertFavoriteProperty($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_favorite_property( user_id, property_id, created_at )
                                        VALUES
                                        ( :user_id, :property_id, :created_at)');

        $result = $stmt->execute(
            array(
                ':user_id' => $itm->user_id,                
                ':property_id' => $itm->property_id,                
                ':created_at' => $itm->created_at));

        return $result ? true : false;
    }
	
	public function removeFavoriteProperty($itm)	
    {	
        $stmt = $this->pdo->prepare('DELETE from tbl_favorite_property WHERE user_id = :user_id AND property_id = :property_id LIMIT 1');	
        $result = $stmt->execute(	
            array(	
                ':user_id' => $itm->user_id,                	
                ':property_id' => $itm->property_id                	
                ));	
        return $result ? true : false;	
    }

}