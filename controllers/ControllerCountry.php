<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerCountry
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

    public function insertCountry($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_country( country_name,created_at )
                                        VALUES
                                        ( :country_name, NOW() )');

        $result = $stmt->execute(
            array('country_name' => $itm->country_name));

        return $result ? true : false;
    }


    public function getcountry($search)
    {
        $where = '';
        $bindParams = [];
        if(!empty($search)){
            if(isset($search['search_text'])){
                $where .= " AND country_name LIKE '%".$search['search_text']."%' ";
                //$bindParams['country_name'] = $search['search_text'];
            }
        }        
        
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_country 
                                where 1 = 1 '.$where.' ORDER BY country_id ASC');
        
        
        $stmt->execute($bindParams);
        
        $array = array();
        $ind = 0;
       
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Country();
            $itm->country_id = $row['country_id'];
            $itm->country_name = $row['country_name'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    

    public function deleteCountry($id)
    {
        $stmt = $this->pdo->prepare('DELETE from tbl_country WHERE country_id = :id ');

        $result = $stmt->execute(
            array(
                ':id' => $id,                
            )
        );

        return $result ? true : false;
    }

    public function getCountryByCountryId($id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_country 
                                WHERE country_id = :id');

        $stmt->execute(
            array(
                ':id' => $id
            )
        );

        foreach ($stmt as $row) {
            $itm = $this->formatCountry($row);
            return $itm;
        }

        return null;
    }

    public function formatCountry($row)
    {
        $itm = new Country();
        $itm->country_id = $row['country_id'];
        $itm->country_name = $row['country_name'];
        return $itm;
    }

    public function updateCountry($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_country
                                        SET  country_name = :country_name,
                                        updated_at = NOW()
                                        WHERE country_id = :country_id');

        $result = $stmt->execute(
            array(
                ':country_id' => $itm->country_id,
                ':country_name' => $itm->country_name,                
            )
        );

        return $result ? true : false;

    }
}