<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerMedia
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

    public function insertMedia($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_media( file_name, thumb_name, ref_id, ref_table, created_at )
                                        VALUES
                                        ( :file_name, :thumb_name, :ref_id, :ref_table, :created_at )');

        $result = $stmt->execute(
            array(
                ':file_name' => $itm->file_name,
                ':thumb_name' => $itm->thumb_name,
                ':ref_id' => $itm->ref_id,
                ':ref_table' => $itm->ref_table,                
                ':created_at' => $itm->created_at
                ));

        return $result ? true : false;
    }

    public function getLastInsertedId()
    {
        $query = $this->pdo->prepare('SELECT *  FROM tbl_media order by media_id DESC LIMIT 1');
        $query->execute();
        $row = $query->fetch();
        return $row;
    }
}