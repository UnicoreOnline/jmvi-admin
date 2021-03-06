<?php
 
class ControllerPropertyType
{ 
    private $db;
    private $pdo;
    function __construct() 
    {
        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();
    }
 
    function __destruct() { }
 
    public function updatePropertyType($itm) 
    {
        
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_propertytypes 
                                        SET 
                                            property_type = :property_type, 
                                            updated_at = :updated_at 
                                        WHERE propertytype_id = :propertytype_id');

        $result = $stmt->execute(
                            array('property_type' => $itm->property_type, 
                                    'updated_at' => $itm->updated_at, 
                                    'propertytype_id' => $itm->propertytype_id) );
        
        return $result ? true : false;
    }

    public function deletePropertyType($propertytype_id, $is_deleted) 
    {

        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_propertytypes 
                                        SET is_deleted = :is_deleted
                                        WHERE propertytype_id = :propertytype_id');


        $result = $stmt->execute(
                            array('is_deleted' => $is_deleted, 
                                    'propertytype_id' => $propertytype_id) );
        
        return $result ? true : false;
    }

    public function insertPropertyType($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_realestate_propertytypes( property_type, created_at, updated_at ) 
                                        
                                        VALUES
                                        ( :property_type, :created_at, :updated_at )');
        
        $result = $stmt->execute(
                            array('property_type' => $itm->property_type,
                                    'created_at' => $itm->created_at,
                                    'updated_at' => $itm->updated_at) );
        
        return $result ? true : false;
    }
 
    
    public function getPropertyTypes() 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_propertytypes 
                                 WHERE is_deleted = 0 ORDER BY property_type ASC');

        $stmt->execute();

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new PropertyType();
            $itm->propertytype_id = $row['propertytype_id'];
            $itm->property_type = $row['property_type'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function getPropertyTypesBySearching($search) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_realestate_propertytypes 
                                        WHERE is_deleted = 0 AND property_type LIKE :search ORDER BY property_type ASC');

        $stmt->execute( array('search' => '%'.$search.'%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new PropertyType();
            $itm->propertytype_id = $row['propertytype_id'];
            $itm->property_type = $row['property_type'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }


    public function getPropertyTypeByPropertyTypeId($propertytype_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_realestate_propertytypes WHERE propertytype_id = :propertytype_id');

        $stmt->execute( array('propertytype_id' => $propertytype_id));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new PropertyType();
            $itm->propertytype_id = $row['propertytype_id'];
            $itm->property_type = $row['property_type'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            return $itm;
        }
        return null;
    }


    public function getPropertyTypeByPropertyType($property_type) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_realestate_propertytypes WHERE property_type = :property_type');

        $stmt->execute( array('property_type' => $property_type));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new PropertyType();
            $itm->propertytype_id = $row['propertytype_id'];
            $itm->property_type = $row['property_type'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];

            return $itm;
        }
        return null;
    }


}
 
?>