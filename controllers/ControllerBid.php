<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 20/2/18
 * Time: 12:59 AM
 */

class ControllerBid
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

    public function insertBid($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_bid( auction_id, user_id, name, currency, bid_amount, created_at, updated_at )
                                        VALUES
                                        ( :auction_id, :user_id, :name, :currency, :bid_amount,:created_at, :updated_at )');

        $result = $stmt->execute(
            array(
                'auction_id' => $itm->auction_id,
                'user_id' => $itm->user_id,
                'name' => $itm->name,
                'currency' => $itm->currency,
                'bid_amount' => $itm->bid_amount,
                'created_at' => $itm->created_at,
                'updated_at' => $itm->updated_at));

        return $result ? true : false;
    }

    public function getLastInsertedId()
    {
        $query = $this->pdo->prepare('SELECT auction_id, user_id,currency, bid_amount, name FROM tbl_bid order by id DESC LIMIT 1');
        $query->execute();
        $row = $query->fetch();
        return $row;
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
        $itm->is_allowed = $row['is_allowed'];	
        return $itm;	
    }	
    public function checkAccessForBid($propertyId, $userId)	
    {	
        $stmt = $this->pdo->prepare('	
            SELECT trp.* FROM tbl_reserved_property as trp 	
                                        WHERE property_id = :pid AND user_id = :uid AND is_deleted = 0 AND is_allowed = 1');	
        $stmt->execute( array('pid' => $propertyId, 'uid' => $userId));	
        foreach ($stmt as $row)	
        {	
            $itm = $this->formatReservedProperty($row);	
            return $itm->is_allowed;	
        }	
        return 0;	
    }
}