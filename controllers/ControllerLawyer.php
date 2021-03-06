<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerLawyer
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

    public function insertLawyer($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_lawyer( name, email, contact_no, company_name, address, website, whatsapp_no, photo_url,thumb_url, created_at )
                                        VALUES
                                        ( :name, :email, :contact_no, :company_name, :address, :website, :whatsapp_no, :photo_url, :thumb_url, :created_at )');

        $result = $stmt->execute(
            array(
                ':name' => $itm->name,
                ':email' => $itm->email,
                ':contact_no' => $itm->contact_no,
                ':company_name' => $itm->company_name,
                ':address' => $itm->address,
                ':website' => $itm->website,
                ':whatsapp_no' => $itm->whatsapp_no,
                ':photo_url' => $itm->photo_url,
                ':thumb_url' => $itm->thumb_url,
                ':created_at' => $itm->created_at));

        return $result ? true : false;
    }

    public function getLastInsertedId()
    {
        $query = $this->pdo->prepare('SELECT *  FROM tbl_lawyer order by lawyer_id DESC LIMIT 1');
        $query->execute();
        $row = $query->fetch();
        return $row;
    }

    public function getLawyers()
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_lawyer 
                                 WHERE is_deleted = 0 ORDER BY lawyer_id DESC');

        $stmt->execute();
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Lawyer();
            $itm->lawyer_id = $row['lawyer_id'];            
            $itm->name = $row['name'];
            $itm->email = $row['email'];
            $itm->contact_no = $row['contact_no'];
            $itm->company_name = $row['company_name'];
            $itm->address = $row['address'];
            $itm->website = $row['website'];
            $itm->whatsapp_no = $row['whatsapp_no'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function getLawyersBySearching($search)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_lawyer 
                                        WHERE is_deleted = 0 AND (name LIKE :search OR email LIKE :search_email) ORDER BY lawyer_id DESC');

        $stmt->execute(array(':search' => '%' . $search . '%',':search_email' => '%' . $search . '%'));
        
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Lawyer();
            $itm->lawyer_id = $row['lawyer_id'];            
            $itm->name = $row['name'];
            $itm->email = $row['email'];
            $itm->contact_no = $row['contact_no'];
            $itm->company_name = $row['company_name'];
            $itm->address = $row['address'];
            $itm->website = $row['website'];
            $itm->whatsapp_no = $row['whatsapp_no'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function deleteLawyer($id, $deleted_at)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_lawyer
                                        SET is_deleted = 1
                                        WHERE lawyer_id = :id ');

        $result = $stmt->execute(
            array(
                ':id' => $id,                
            )
        );

        return $result ? true : false;
    }

    public function getLawyerByLawyerId($id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_lawyer 
                                WHERE lawyer_id = :id');

        $stmt->execute(
            array(
                ':id' => $id
            )
        );

        foreach ($stmt as $row) {
            $itm = $this->formatLawyer($row);
            return $itm;
        }

        return null;
    }

    public function formatLawyer($row)
    {
        
        $itm = new Lawyer();
        $itm->lawyer_id = $row['lawyer_id'];            
        $itm->name = $row['name'];
        $itm->email = $row['email'];
        $itm->contact_no = $row['contact_no'];
        $itm->company_name = $row['company_name'];
        $itm->address = $row['address'];
        $itm->website = $row['website'];
        $itm->whatsapp_no = $row['whatsapp_no'];
        $itm->photo_url = $row['photo_url'];
        $itm->thumb_url = $row['thumb_url'];
        $itm->created_at = $row['created_at'];
        $itm->updated_at = $row['updated_at'];        
        
        return $itm;
    }

    public function updateLawyer($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_lawyer
                                        SET name = :name, 
                                            email = :email, 
                                            contact_no = :contact_no, 
                                            company_name = :company_name, 
                                            address = :address, 
                                            website = :website, 
                                            whatsapp_no = :whatsapp_no, 
                                            photo_url = :photo_url, 
                                            thumb_url = :thumb_url, 
                                            updated_at = :updated_at
                                        WHERE lawyer_id = :id');

        $result = $stmt->execute(
            array(
                ':id' => $itm->lawyer_id,
                ':name' => $itm->name,
                ':email' => $itm->email,
                ':contact_no' => $itm->contact_no,
                ':company_name' => $itm->company_name,
                ':address' => $itm->address,
                ':website' => $itm->website,
                ':whatsapp_no' => $itm->whatsapp_no,
                ':photo_url' => $itm->photo_url,
                ':thumb_url' => $itm->thumb_url,
                ':updated_at' => $itm->updated_at
            )
        );
        
        

        return $result ? true : false;

    }
    
    
}