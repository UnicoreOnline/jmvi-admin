<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerBank
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

    public function insertBank($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_bank( bank_name, address, branch_location, operation_hours, contact_number, mortgage_dep_number, created_at, updated_at )
                                        VALUES
                                        ( :bank_name, :address, :branch_location, :operation_hours, :contact_number, :mortgage_dep_number,:created_at, :updated_at )');

        $result = $stmt->execute(
            array(
                ':bank_name' => $itm->bank_name,
                ':address' => $itm->address,
                ':branch_location' => $itm->branch_location,
                ':operation_hours' => $itm->operation_hours,
                ':contact_number' => $itm->contact_number,
                ':mortgage_dep_number' => $itm->mortgage_dep_number,
                ':created_at' => $itm->created_at,
                ':updated_at' => $itm->updated_at));

        return $result ? true : false;
    }

    public function getLastInsertedId()
    {
        $query = $this->pdo->prepare('SELECT *  FROM tbl_bank order by id DESC LIMIT 1');
        $query->execute();
        $row = $query->fetch();
        return $row;
    }

    public function getBank()
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_bank 
                                 WHERE is_deleted = 0 ORDER BY bank_name ASC');

        $stmt->execute();
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Bank();
            $itm->id = $row['id'];            
            $itm->bank_name = $row['bank_name'];
            $itm->address = $row['address'];
            $itm->branch_location = $row['branch_location'];
            $itm->operation_hours = $row['operation_hours'];
            $itm->contact_number = $row['contact_number'];
            $itm->mortgage_dep_number = $row['mortgage_dep_number'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function getBankBySearching($search)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_bank 
                                        WHERE is_deleted = 0 AND bank_name LIKE :search ORDER BY bank_name ASC');

        $stmt->execute(array('search' => '%' . $search . '%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Bank();
            $itm->id = $row['id'];
            $itm->bank_name = $row['bank_name'];
            $itm->address = $row['address'];
            $itm->branch_location = $row['branch_location'];
            $itm->operation_hours = $row['operation_hours'];
            $itm->contact_number = $row['contact_number'];
            $itm->mortgage_dep_number = $row['mortgage_dep_number'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function deleteBank($id, $deleted_at)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_bank
                                        SET is_deleted = 1
                                        WHERE id = :id ');

        $result = $stmt->execute(
            array(
                ':id' => $id,                
            )
        );

        return $result ? true : false;
    }

    public function getBankByBankId($id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_bank 
                                WHERE id = :id');

        $stmt->execute(
            array(
                ':id' => $id
            )
        );

        foreach ($stmt as $row) {
            $itm = $this->formatBank($row);
            return $itm;
        }

        return null;
    }

    public function formatBank($row)
    {
        $itm = new Bank();
        $itm->id = $row['id'];
        $itm->bank_name = $row['bank_name'];
        $itm->address = $row['address'];
        $itm->branch_location = $row['branch_location'];
        $itm->operation_hours = $row['operation_hours'];
        $itm->contact_number = $row['contact_number'];
        $itm->mortgage_dep_number = $row['mortgage_dep_number'];
        $itm->created_at = $row['created_at'];
        $itm->updated_at = $row['updated_at'];
        return $itm;
    }

    public function updateBank($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_bank
                                        SET bank_name = :bank_name, 
                                            address = :address, 
                                            branch_location = :branch_location, 
                                            operation_hours = :operation_hours, 
                                            contact_number = :contact_number, 
                                            mortgage_dep_number = :mortgage_dep_number, 
                                            updated_at = :updated_at
                                        WHERE id = :id');

        $result = $stmt->execute(
            array(
                ':id' => $itm->id,
                ':bank_name' => $itm->bank_name,
                ':address' => $itm->address,
                ':branch_location' => $itm->branch_location,
                ':operation_hours' => $itm->operation_hours,
                ':contact_number' => $itm->contact_number,
                ':mortgage_dep_number' => $itm->mortgage_dep_number,
                ':updated_at' => $itm->updated_at
            )
        );
        
        

        return $result ? true : false;

    }
    
    public function updateBankLogo($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_bank
                                        SET logo = :logo
                                        WHERE id = :id');

        $result = $stmt->execute(
            array(
                ':id' => $itm->id,
                ':logo' => $itm->logo
            )
        );
        
        

        return $result ? true : false;

    }
    
    public function deleteMdeia($media_id, $is_deleted) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_media 
                                
                                        SET 
                                            is_deleted = :is_deleted 

                                        WHERE media_id = :media_id');
        
        $result = $stmt->execute(
                
                            array('media_id' =>$media_id,
                                    'is_deleted' => $is_deleted ) );
        
        
        return $result ? true : false;
    }

    public function getPhotosByBankId($ref_id,$ref_table) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_media 
                                        WHERE ref_id = :ref_id AND ref_table = :ref_table AND is_deleted = 0');

        $result = $stmt->execute(
                            array('ref_id' =>$ref_id, 'ref_table'=>$ref_table) );

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $media = new Media();
            $media->media_id = $row['media_id'];
            $media->file_name = $row['file_name'];                
            $media->ref_table = $row['ref_table'];
            $media->ref_id = $row['ref_id'];
            $media->created_at = $row['created_at'];

            $array[$ind] = $media;
            $ind++;
        }
        return $array;
    }
    
}