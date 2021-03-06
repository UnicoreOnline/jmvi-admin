<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerBanner
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

    public function insertBanner($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_banner( banner_name,link, created)
                                        VALUES
                                        ( :banner_name,:link, NOW() )');

        $result = $stmt->execute(
            array(
                ':banner_name' => $itm->banner_name,
                ':link' => $itm->link,
                ));

        return $result ? true : false;
    }

    public function getLastInsertedId()
    {
        $query = $this->pdo->prepare('SELECT *  FROM tbl_banner order by id DESC LIMIT 1');
        $query->execute();
        $row = $query->fetch();
        return $row;
    }

    public function getBanner()
    {
        $stmt = $this->pdo->prepare('SELECT *, CONCAT("https://jmviapp.com/upload_pic/banner/", banner_name) as banner_url 
                                FROM tbl_banner 
                                 ORDER BY id ASC');

        $stmt->execute();
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Banner();
            $itm->id = $row['id'];            
            $itm->banner_name = $row['banner_name'];
            $itm->banner_url = $row['banner_url'];
            $itm->link = $row['link'];
            $itm->created = $row['created'];
            $itm->updated = $row['updated'];
            
            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    
    public function deleteBanner($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM tbl_banner WHERE id = :id ');

        $result = $stmt->execute(
            array(
                ':id' => $id,                
            )
        );

        return $result ? true : false;
    }

    public function getBannerByBannerId($id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_banner 
                                WHERE id = :id');

        $stmt->execute(
            array(
                ':id' => $id
            )
        );

        foreach ($stmt as $row) {
            $itm = $this->formatBanner($row);
            return $itm;
        }

        return null;
    }

    public function formatBanner($row)
    {
        $itm = new Bank();
        $itm->id = $row['id'];            
        $itm->banner_name = $row['banner_name'];
        $itm->link = $row['link'];
        $itm->created = $row['created'];
        $itm->updated = $row['updated'];
        return $itm;
    }

    public function updateBank($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_banner
                                        SET banner_name = :banner_name,                                              
                                         link = :link,                                              
                                            updated = NOW()
                                        WHERE id = :id');

        $result = $stmt->execute(
            array(
                ':id' => $itm->id,
                ':banner_name' => $itm->banner_name,                
                ':link' => $itm->link                
            )
        );
        
        

        return $result ? true : false;

    }
}