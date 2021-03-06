<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 16/5/18
 * Time: 11:04 PM
 */
class ControllerBidMade
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

    public function getBidMade($params = [])
    {
        
        $bindParams = [];
        $where = ' ';
        if(isset($params['auction_id']) && (int)$params['auction_id'] > 0){
            $bindParams['auction_id'] = $params['auction_id'];
            $where .= ' AND tbl_bid.auction_id = :auction_id ';
        }
		
		if(!empty($params["keyword"])) {
			$keyword = $params["keyword"];
			$where .= ' AND ( trr.pname like  "%'.$keyword.'%" OR tru.full_name like  "%'.$keyword.'%" OR tru.mobile like  "%'.$keyword.'%")';
		}
        
        $stmt = $this->pdo->prepare('SELECT tbl_bid.* 
                                        FROM tbl_bid
										left join  tbl_auction as ta on ta.id = tbl_bid.auction_id	
										left join  tbl_realestate_realestates as trr on trr.realestate_id = ta.property_id	
										left join  tbl_realestate_users as tru on tru.user_id = tbl_bid.user_id	
										WHERE tbl_bid.deleted_at = 0 '.$where.'
                                        ORDER BY id DESC');

        $stmt->execute($bindParams);

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            $itm = $this->formatBidMade($row);

            $array[$ind] = $itm;
            $ind++;
        }

        return $array;
    }

    public function deleteBidMade($id, $deleted_at)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_bid 
                                        SET deleted_at = :deleted_at 
                                        WHERE id = :id ');

        $result = $stmt->execute(
            array(
                'deleted_at' => $deleted_at,
                'id' => $id
            )
        );

        return $result ? true : false;
    }

    public function formatBidMade($row)
    {
        $itm = new BidMade();
        $itm->id = $row['id'];
        $itm->user_id = $row['user_id'];
        $itm->auction_id = $row['auction_id'];
        $itm->currency = $row['currency'];
        $itm->bid_amount = $row['bid_amount'];
        $itm->name = $row['name'];
        $itm->created_at = $row['created_at'];
        $itm->invoice = $row['invoice'];
        return $itm;
    }

    public function getBidById($id)
    {

        $stmt = $this->pdo->prepare('
            SELECT tb.*,trr.address as propery_address,trr.pname as property_name,trr.address as propery_address,trr.price,trr.currency, tru.address as user_address,trr.pdes,tru.full_name as user_name,tru.mobile,ta.property_id
                                        FROM tbl_bid as tb 
                                        LEFT JOIN tbl_auction as ta ON ta.id = tb.auction_id
                                        LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = ta.property_id
                                        LEFT JOIN tbl_realestate_users as tru ON tru.user_id = tb.user_id 
                                        WHERE tb.deleted_at = 0 AND tb.id = :id
                                        GROUP BY tb.id
                                        ORDER BY tb.id DESC');

        $stmt->execute( array('id' => $id));
        
        foreach ($stmt as $row)
        {            
            //$itm = $this->formatBidMade($row);
            $itm = $row;
            return $itm;
        }

        return null;
    }
    
    public function getBidMadeById($id)
    {

        $stmt = $this->pdo->prepare('
            SELECT tb.*,trr.address as propery_address,trr.pname as property_name,trr.address as propery_address,trr.price,trr.currency, tru.address as user_address,trr.pdes,tru.full_name as user_name,tru.mobile
                                        FROM tbl_bid as tb 
                                        LEFT JOIN tbl_auction as ta ON ta.id = tb.auction_id
                                        LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = ta.property_id
                                        LEFT JOIN tbl_realestate_users as tru ON tru.user_id = tb.user_id 
                                        WHERE tb.deleted_at = 0 AND tb.id = :id
                                        GROUP BY tb.id
                                        ORDER BY tb.id DESC');

        $stmt->execute( array('id' => $id));
        
        foreach ($stmt as $row)
        {            
            $itm = $this->formatBidMade($row);            
            return $itm;
        }

        return null;
    }
    
    public function updateInvoice($itm)
    {
        //invoice
        $stmt = $this->pdo->prepare('UPDATE tbl_bid
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
    
}