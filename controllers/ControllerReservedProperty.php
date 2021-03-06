<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 13/4/18
 * Time: 9:59 PM
 */

class ControllerReservedProperty
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

    public function insertReservedProperty($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_reserved_property( user_id, user_name, user_email, mobile, user_address, property_id, property_name, property_status,created_at,is_allowed )
                                        VALUES
                                        ( :user_id, :user_name, :user_email, :mobile, :user_address, :property_id, :property_name, :property_status, :created_at,0)');

        $result = $stmt->execute(
            array(
                'user_id' => $itm->user_id,
                'user_name' => $itm->user_name,
                'user_email' => $itm->user_email,
                'mobile' => $itm->mobile,
                'property_id' => $itm->property_id,
                'user_address' => $itm->user_address,
                'property_name' => $itm->property_name,
                'property_status' => $itm->property_status,
                'created_at' => $itm->created_at));

        return $result ? true : false;
    }

    public function getReservedProperty($searchParams)
    {
        
        $bindParams = [];
        $where = ' ';        
        if(isset($searchParams['status']) && $searchParams['status'] != ''){
            $bindParams['status'] = $searchParams['status'];
            $where .= ' AND trr.status = :status ';
        }
        
        $stmt = $this->pdo->prepare('SELECT trp.*,trr.address as propery_address,trr.price,trr.currency, tru.address as user_address
                                        FROM tbl_reserved_property as trp 
                                        LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = trp.property_id
                                        LEFT JOIN tbl_realestate_users as tru ON tru.user_id = trp.user_id 
                                        WHERE trp.is_deleted = 0 '.$where.'
                                        GROUP BY trp.id
                                        ORDER BY trp.id DESC');
        $stmt->execute($bindParams);

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $itm = $this->formatReservedProperty($row);

            $array[$ind] = $itm;
            $ind++;
        }

        return $array;
    }

    public function deleteReservedProperty($id, $is_deleted)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_reserved_property 
                                        SET is_deleted = :is_deleted 
                                        WHERE id = :id ');

        $result = $stmt->execute(
            array(
                'is_deleted' => $is_deleted,
                'id' => $id
            )
        );

        return $result ? true : false;

    }

    public function formatReservedProperty($row)
    {
        $itm = new ReservedProperty();
        $itm->id = $row['id'];
        $itm->user_id = $row['user_id'];
        $itm->user_name = $row['user_name'];
        $itm->user_email = $row['user_email'];
        $itm->user_address = $row['user_address'];
        $itm->mobile = $row['mobile'];
        $itm->property_id = $row['property_id'];
        $itm->property_name = $row['property_name'];
        $itm->property_status = $row['property_status'];
        $itm->created_at = $row['created_at'];
        $itm->invoice = $row['invoice'];
        $itm->is_allowed =  $row['is_allowed'];
        if(isset($row['propery_address'])){
            $itm->propery_address = $row['propery_address'];
        }
        if(isset($row['price'])){
            $itm->price = $row['price'];
        }
        if(isset($row['currency'])){
            $itm->currency = $row['currency'];
        }
        
        return $itm;
    }

    public function getReservedPropertyById($id)
    {

        $stmt = $this->pdo->prepare('
            SELECT trp.*,trr.address as propery_address,trr.price,trr.currency, tru.address as user_address,trr.pdes
                                        FROM tbl_reserved_property as trp 
                                        LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = trp.property_id
                                        LEFT JOIN tbl_realestate_users as tru ON tru.user_id = trp.user_id 
                                        WHERE trp.is_deleted = 0 AND id = :id
                                        GROUP BY trp.id
                                        ORDER BY trp.id DESC');

        $stmt->execute( array('id' => $id));

        foreach ($stmt as $row)
        {
            $itm = $this->formatReservedProperty($row);
            return $itm;
        }

        return null;
    }
    
    
    public function updateInvoice($itm)
    {
        //invoice
        $stmt = $this->pdo->prepare('UPDATE tbl_reserved_property
                                        SET invoice = :invoice
                                        WHERE id = :id');

        $result = $stmt->execute(
            array(
                ':id' => $itm->id,
                ':invoice' => $itm->invoice,               
            )
        );        

        return $result ? true : false;
    }

    public function getGroupedReservedProperty($page = 1,$keyword = "",$noFilter = false)
    {
        
        $bindParams = [];
        $where = ' ';        
        $limit = 10;

        $offset = (($page-1) * $limit).",".$limit;
		if(!empty($keyword)) {
		    $where = ' and ( trp.user_name like  "%'.$keyword.'%" or  trp.property_name like  "%'.$keyword.'%" or  trp.user_email like  "%'.$keyword.'%" or  trp.mobile like  "%'.$keyword.'%")';			
		} 	
	
        
        $rCount = $this->pdo->query('SELECT count(*)
                                        FROM tbl_reserved_property as trp 
                                        LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = trp.property_id
                                        LEFT JOIN tbl_realestate_users as tru ON tru.user_id = trp.user_id 
                                        WHERE trp.is_deleted = 0 AND trr.status = 3 '.$where.'
                                        ORDER BY trp.id DESC')->fetchColumn();

        $query = 'SELECT trp.*,trr.address as propery_address,trr.price,trr.currency, tru.address as user_address
                    FROM tbl_reserved_property as trp 
                    LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = trp.property_id
                    LEFT JOIN tbl_realestate_users as tru ON tru.user_id = trp.user_id 
                    WHERE trp.is_deleted = 0  AND trr.status = 3 '.$where.'
                    GROUP BY trp.id
                    ORDER BY trp.id DESC';
        if (!$noFilter) {
            $query .= ' LIMIT '.$offset;
        }
        $stmt = $this->pdo->prepare($query);

        $stmt->execute($bindParams);

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $itm = $this->formatReservedProperty($row);

            $array[$ind] = $itm;
            $ind++;
        }

        return ['total' => $rCount,'records' => $array];
    }

    public function updateRegisteredBidder($propertyId, $userId, $isAllowed)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_reserved_property 
                                        SET is_allowed = :is_allowed 
                                        WHERE property_id = :pid AND user_id = :uid');

        $result = $stmt->execute(
            array(
                'is_allowed' => $isAllowed,
                'pid' => $propertyId,
                'uid' => $userId
            ));

        return $result ? true : false;
    }
    
    public function checkAccessForBid($propertyId, $userId)
    {

        $stmt = $this->pdo->prepare('
            SELECT trp.* FROM tbl_reserved_property as trp 
                                        WHERE property_id = :pid AND user_id = :uid AND is_deleted = 0');

        $result = $stmt->execute( array('pid' => $propertyId, 'uid' => $userId));
		
		foreach ($stmt as $row)
        {
		    $itm = $this->formatReservedProperty($row);
			return 2;
        }

        return 1;
    }
}