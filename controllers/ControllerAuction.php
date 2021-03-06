<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerAuction
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

    public function insertAuction($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_auction( property_id, estimate_price, starting_bid, start_time, end_time, created_at, updated_at )
                                        VALUES
                                        ( :property_id, :estimate_price, :starting_bid, :start_time, :end_time,:created_at, :updated_at )');

        $result = $stmt->execute(
            array(
                'property_id' => $itm->property_id,
                'estimate_price' => $itm->estimate_price,
                'starting_bid' => $itm->starting_bid,
                'start_time' => $itm->start_time,
                'end_time' => $itm->end_time,
                'created_at' => $itm->created_at,
                'updated_at' => $itm->updated_at));

        return $result ? true : false;
    }


    public function getAuction($params = [])
    {
        $bindPrams = [];
        $where = '';
        if(isset($params['search']) && $params['search'] != ''){
            $bindPrams = array('search' => '%' . $params['search'] . '%');
            $where .= " AND trr.address LIKE :search ";
        }
        
        
        $stmt = $this->pdo->prepare('SELECT ta.*, 
                                trr.pname,
                                trr.country,
                                trr.address,
                                trr.pdes,
                                trr.location,
                                trr.property_type,
                                trr.price,
                                trr.price_per_sqft,
                                trr.agent_id,
                                trr.baths,
                                trr.beds,
                                trr.built_in,
                                trr.rooms,
                                trr.sqft,
                                trr.lot_size,
                                trr.featured,          
                                trr.currency,
                                pt.property_type AS property_type_str,
                                tra.name as agent_name,
                                COUNT(DISTINCT tb.id) as total_bid,
                                MAX(tb.bid_amount) as highest_bid
                                FROM tbl_auction as ta
                                LEFT JOIN tbl_bid as tb ON tb.auction_id = ta.id AND tb.deleted_at = 0
                                LEFT JOIN tbl_realestate_realestates as trr ON trr.realestate_id = ta.property_id
                                LEFT JOIN tbl_realestate_propertytypes as pt ON pt.propertytype_id = trr.property_type
                                LEFT JOIN tbl_realestate_agents as tra ON tra.agent_id = trr.agent_id                                
                                WHERE ta.deleted_at = 0 '.$where.' group by ta.id ORDER BY ta.id ASC');

        $stmt->execute($bindPrams);
        
        
        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Auction();
            $itm->id = $row['id'];
            $itm->property_id = $row['property_id'];
            $itm->estimate_price = $row['estimate_price'];
            $itm->starting_bid = $row['starting_bid'];
            $itm->start_time = $row['start_time'];
            $itm->end_time = $row['end_time'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];
            $itm->is_start_bid = $row['is_start_bid'];

            
            $itm->address = $row['address'];
            $itm->country = $row['country'];
            $itm->agent_id = $row['agent_id'];
            $itm->baths = $row['baths'];
            $itm->beds = $row['beds'];
            $itm->built_in = $row['built_in'];            
            $itm->featured = $row['featured'];           
            $itm->lot_size = $row['lot_size'];
            $itm->price = $row['price'];
            $itm->price_per_sqft = $row['price_per_sqft'];
            $itm->property_type = $row['property_type'];            
            $itm->rooms = $row['rooms'];
            $itm->sqft = $row['sqft'];            
            $itm->currency = $row['currency'];
            $itm->pname = $row['pname'];
            $itm->pdes = $row['pdes'];
            $itm->location = $row['location'];
            if(isset($row['property_type_str'])){
                $itm->property_type_str = $row['property_type_str'];
            }
            if(isset($row['agent_name'])){
                $itm->agent_name = $row['agent_name'];
            }
            if(isset($row['total_bid'])){
                $itm->total_bid = $row['total_bid'];
            }
            if(isset($row['highest_bid'])){
                $itm->highest_bid = $row['highest_bid'];
            }
            
            //total_bid,highest_bid
            
            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function getAuctionBySearching($search)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_auction 
                                        WHERE is_deleted = 0 AND property_address LIKE :search ORDER BY property_address ASC');

        $stmt->execute(array('search' => '%' . $search . '%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new Auction();
            $itm->id = $row['id'];
            $itm->property_address = $row['property_address'];
            $itm->estimate_price = $row['estimate_price'];
            $itm->starting_bid = $row['starting_bid'];
            $itm->start_time = $row['start_time'];
            $itm->end_time = $row['end_time'];
            $itm->created_at = $row['created_at'];
            $itm->updated_at = $row['updated_at'];
            $itm->is_start_bid = $row['is_start_bid'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function deleteAuction($id, $deleted_at)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_auction
                                        SET deleted_at = :deleted_at
                                        WHERE id = :id ');

        $result = $stmt->execute(
            array(
                ':id' => $id,
                ':deleted_at' => $deleted_at
            )
        );

        return $result ? true : false;
    }

    public function getAuctionByAuctionId($id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_auction 
                                WHERE id = :id');

        $stmt->execute(
            array(
                ':id' => $id
            )
        );

        foreach ($stmt as $row) {
            $itm = $this->formatAuction($row);
            return $itm;
        }

        return null;
    }

    public function formatAuction($row)
    {
        $itm = new Auction();
        $itm->id = $row['id'];
        $itm->property_id = $row['property_id'];
        $itm->estimate_price = $row['estimate_price'];
        $itm->starting_bid = $row['starting_bid'];
        $itm->start_time = $row['start_time'];
        $itm->end_time = $row['end_time'];
        $itm->created = $row['created_at'];
        $itm->updated_at = $row['updated_at'];
        $itm->is_start_bid = $row['is_start_bid'];
        return $itm;
    }

    public function updateAuction($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_auction

                                        SET property_id = :property_id, 
                                            currency = :currency, 
                                            estimate_price = :estimate_price, 
                                            starting_bid = :starting_bid, 
                                            start_time = :start_time, 
                                            end_time = :end_time, 
                                            updated_at = :updated_at
                                        WHERE id = :id');

        $result = $stmt->execute(
            array(
                ':id' => $itm->id,
                ':property_id' => $itm->property_id,
                ':currency' => $itm->currency,
                ':estimate_price' => $itm->estimate_price,
                ':starting_bid' => $itm->starting_bid,
                ':start_time' => $itm->start_time,
                ':end_time' => $itm->end_time,
                ':updated_at' => $itm->updated_at
            )
        );

        return $result ? true : false;

    }
    
     public function updateAuctionStartBid($auction_id, $start_bid)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_auction 
                                        SET is_start_bid = :is_start_bid 
                                        WHERE id = :id');

        $result = $stmt->execute(
            array('is_start_bid' => $start_bid,
                'id' => $auction_id));

        return $result ? true : false;
    }
}