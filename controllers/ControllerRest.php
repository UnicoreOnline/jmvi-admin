<?php


class ControllerRest
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

    public function getPhotosResult()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_photos WHERE is_deleted = 0');

        $stmt->execute();
        return $stmt;
    }

    public function getAgentsResult($country = '')
    {
        $bindPrams['country'] = '%' . $country . '%';
        if(!empty($country)){
            $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_agents WHERE is_deleted = 0 AND (country LIKE :country) ORDER BY name ASC');
            $stmt->execute($bindPrams);
        } else {
            $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_agents WHERE is_deleted = 0 ORDER BY name ASC');
            $stmt->execute();
        }
        
        return $stmt;
    }

    public function getRealEstateResult()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_realestates WHERE is_deleted = 0');
        $stmt->execute();
        return $stmt;
    }

    public function getCountriesResult()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_countries WHERE is_deleted = 0');
        $stmt->execute();
        return $stmt;
    }

    public function getPropertyTypeResult()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_propertytypes WHERE is_deleted = 0');
        $stmt->execute();
        return $stmt;
    }

    public function getResultRealEstateByRealEstateId($realestate_id, $country = '')
    {
        $extraWhere = '';
        $bindPrams = array('realestate_id' => $realestate_id);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        $stmt = $this->pdo->prepare('SELECT tbl_realestate_realestates.*,IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency,CONCAT(tbl_realestate_realestates.currency," $") as org_currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                    tbl_realestate_propertytypes.property_type AS property_type_str,
                    tbl_realestate_agents.email as agent_email,
                    tbl_realestate_agents.contact_no as agent_contact_no
                FROM tbl_realestate_realestates 
                LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                LEFT JOIN tbl_realestate_agents on tbl_realestate_agents.agent_id = tbl_realestate_realestates.agent_id
                WHERE realestate_id = :realestate_id
                 '.$extraWhere.' 
                GROUP BY tbl_realestate_realestates.realestate_id ');
        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getRealEstateResultRadiusFeatured($lat, $lon, $radius, $user_id = 0, $country = '' )
    {
        
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, /*'radius' => $radius*/);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND featured = 1 
                                     '.$extraWhere.' 
                                    /*HAVING distance <= :radius */
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getRealEstateResultRadiusAgentById($lat, $lon, $radius, $agent_id, $user_id = 0, $country = '')
    {
        
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius, 'agent_id' => $agent_id);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND agent_id = :agent_id 
                                     '.$extraWhere.' 
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getRealEstateResultRadiusStatus($lat, $lon, $radius, $status, $user_id = 0, $country = '')
    {
        
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, /*'radius' => $radius,*/ 'status' => $status);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND status = :status 
                                     '.$extraWhere.' 
                                    /*HAVING distance <= :radius */
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getRealEstateResultRadius($lat, $lon, $radius,$user_id = 0, $country = '')
    {
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 
                                     '.$extraWhere.' 
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getAgentsResultByAgentId($agent_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_agents WHERE is_deleted = 0 AND agent_id = :agent_id');
        $stmt->execute(array('agent_id' => $agent_id));
        return $stmt;
    }

    public function getPhotosResultByRealEstateId($realestate_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_photos WHERE is_deleted = 0 ANd realestate_id = :realestate_id');
        $stmt->execute(array('realestate_id' => $realestate_id));
        return $stmt;
    }

    public function getRealEstateResultRadiusByPropertyTypeId($lat, $lon, $radius, $propertytype_id, $user_id=0, $country = '')
    {
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius, 'propertytype_id' => $propertytype_id);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND property_type = :propertytype_id 
                                     '.$extraWhere.' 
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getRealEstateResultRadiusByAgentId($lat, $lon, $radius, $agent_id, $user_id=0, $country = '')
    {
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'radius' => $radius, 'agent_id' => $agent_id);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND agent_id = :agent_id 
                                     '.$extraWhere.' 
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    HAVING distance <= :radius 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getMaxDistanceFound($lat, $lon)
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                            cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                            sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_realestate_realestates 
                                    WHERE is_deleted = 0 
                                    ORDER BY distance DESC
                                    LIMIT 0, 1');

        $stmt->execute(array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat));
        foreach ($stmt as $row) {
            return $row['distance'];
        }
        return 0;
    }

    public function getMaxDistanceFoundDefaultToCount($lat, $lon, $default_count_to_find_distance)
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                            cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                            sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance 
                                            
                                    FROM tbl_realestate_realestates 
                                    WHERE is_deleted = 0 
                                    ORDER BY distance DESC
                                    LIMIT 0, :default_count_to_find_distance');

        $stmt->execute(array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'default_count_to_find_distance' => $default_count_to_find_distance));
        foreach ($stmt as $row) {
            return $row['distance'];
        }
        return 0;
    }

    public function searchRealEstateResult($params)
    {

        $price_min = $params['price_min'];
        $price_max = $params['price_max'];
        $lot_size_min = $params['lot_size_min'];
        $lot_size_max = $params['lot_size_max'];
        $built_in_min = $params['built_in_min'];
        $built_in_max = $params['built_in_max'];
        $sqft_min = $params['sqft_min'];
        $sqft_max = $params['sqft_max'];
        $baths = $params['baths'];
        $beds = $params['beds'];
        $property_type = $params['property_type'];
        $lat = $params['lat'];
        $lon = $params['lon'];
        $radius = $params['radius'];
        $status = $params['status'];
        $address = $params['address'];
        $country = $params['country'];

        $sql = 'SELECT *,IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price ';

        /*if ($lat != 0 && $lon != 0) {
            $sql .= ', COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                    cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                    sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance ';
        }*/

        $extraJoin = '';
        if($status == 3){
            $sql .= ', tbl_auction.id as auction_id, tbl_auction.id, tbl_auction.property_id, tbl_auction.estimate_price, tbl_auction.starting_bid, tbl_auction.start_time, tbl_auction.end_time ';
            $extraJoin .= ' LEFT JOIN tbl_auction ON tbl_auction.property_id = tbl_realestate_realestates.realestate_id AND tbl_auction.deleted_at = 0 ';
        }
        
        
        $sql .= 'FROM tbl_realestate_realestates '.$extraJoin.' WHERE tbl_realestate_realestates.is_deleted = 0 ';

        $ptr = array();
        if ($status != '') {
            $sql .= 'AND status = :status ';
            $ptr['status'] = $status;
            
        }
        
        if (strlen($address) > 0) {
            $sql .= 'AND (address LIKE :address1 OR country LIKE :address2 OR zipcode LIKE :address3 OR desc1 LIKE :address4) ';
            $ptr['address1'] = '%' . $address . '%';
            $ptr['address2'] = '%' . $address . '%';
            $ptr['address3'] = '%' . $address . '%';
            $ptr['address4'] = '%' . $address . '%';
        }

        if (strlen($country) > 0) {
            $sql .= 'AND (country LIKE :country) ';
            $ptr['country'] = '%' . $country . '%';            
        }
        
        
        if ($price_min >= 0 && $price_max > 0) {
            $sql .= 'AND (price >= :price_min AND price <= :price_max) ';
            $ptr['price_min'] = $price_min;
            $ptr['price_max'] = $price_max;
        }

        if ($lot_size_min >= 0 && $lot_size_max > 0) {

            if($lot_size_min == 100) {
                $lot_size_min = 0;
            }

            $sql .= 'AND (lot_size >= :lot_size_min AND lot_size <= :lot_size_max) ';
            $ptr['lot_size_min'] = $lot_size_min;
            $ptr['lot_size_max'] = $lot_size_max;
        }

        if ($built_in_min >= 0 && $built_in_max > 0) {

            if($built_in_min == 1900) {
                $built_in_min = 0;
            }

            $sql .= 'AND (built_in >= :built_in_min AND built_in <= :built_in_max) ';
            $ptr['built_in_min'] = $built_in_min;
            $ptr['built_in_max'] = $built_in_max;
        }

        if ($sqft_min >= 0 && $sqft_max > 0) {
            $sql .= 'AND (sqft >= :sqft_min AND sqft <= :sqft_max) ';
            $ptr['sqft_min'] = $sqft_min;
            $ptr['sqft_max'] = $sqft_max;
        }

        if ($baths > 0) {
            $sql .= 'AND baths >= :baths ';
            $ptr['baths'] = $baths;
        }

        if ($beds > 0) {
            $sql .= 'AND beds >= :beds ';
            $ptr['beds'] = $beds;
        }

        if ($property_type > 0) {
            $sql .= 'AND property_type = :property_type ';
            $ptr['property_type'] = $property_type;
        }

        /*if ($lat != 0 && $lon != 0) {
            $sql .= 'HAVING distance <= :radius ORDER BY distance ASC';
            $ptr['lat_params'] = $lat;
            $ptr['lat_params1'] = $lat;
            $ptr['lon_params'] = $lon;
            $ptr['radius'] = $radius;
        } else {
            $sql .= 'ORDER BY price ASC';
        }*/

        $sql .= 'ORDER BY price ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ptr);
        return $stmt;
    }

    public function getRealEstateResultByAgentId($lat, $lon, $agent_id, $user_id=0, $country = '')
    {
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, 'agent_id' => $agent_id);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND agent_id = :agent_id 
                                     '.$extraWhere.' 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }

    public function getPropertyTypeResultByPropertyTypeId($propertytype_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_propertytypes WHERE is_deleted = 0 AND propertytype_id = :propertytype_id');
        $stmt->execute(array('propertytype_id' => $propertytype_id));
        return $stmt;
    }

    public function getAuctionResult($auctionParam = [])
    {
        $bindPrams = [];
        $where = '';
        $user_id = 0;
        if(isset($auctionParam['country'])) {
            $bindPrams['country'] = '%' . $auctionParam['country'] . '%';
            $where .= ' AND (trr.country LIKE :country) ';
        }        
        if(isset($auctionParam['user_id'])) {
            $user_id = $auctionParam['user_id'];
        }
        $bindPrams['user_id'] = $user_id;
        $bindPrams['f_user_id'] = $user_id;
        $bindPrams['r_user_id'] = $user_id;
        
        $stmt = $this->pdo->prepare('SELECT ta.*,CONCAT(trr.currency," $") as currency, IF(trr.is_contact_price = 0,trr.price,"") as price,
                        DATE_FORMAT(ta.start_time, "%Y-%m-%d") as start_time,
                        DATE_FORMAT(ta.start_time, "%M %d, %Y %h:%i:%s %p") as auction_start_time,
                        IFNULL(MAX(tb.bid_amount),0) as highest_bid,
                        IFNULL(MAX(tbu.bid_amount),0) as your_highest_bid,
                        IF(IFNULL(tfp.favorite_id,0) > 0, 1,0) as is_favorite,
                        IF(IFNULL(trp.id,0) > 0, 1,0) as is_reserved
                    FROM tbl_auction as ta
                    INNER JOIN tbl_realestate_realestates as trr ON trr.realestate_id = ta.property_id
                    LEFT JOIN tbl_bid as tb ON tb.auction_id = ta.id AND tb.deleted_at = 0
                    LEFT JOIN tbl_bid as tbu ON tbu.auction_id = ta.id AND tb.deleted_at = 0 AND tbu.user_id = :user_id
                    LEFT JOIN tbl_favorite_property as tfp ON tfp.property_id = ta.property_id AND tfp.user_id = :f_user_id
                    LEFT JOIN tbl_reserved_property as trp ON trp.property_id = ta.property_id AND trp.is_deleted = 0 AND trp.user_id = :r_user_id
                    WHERE ta.deleted_at = 0 '.$where.' GROUP BY ta.id ORDER BY ta.id ASC');
        $stmt->execute($bindPrams);
        
        return $stmt;
    }

    public function getBidByAuctionId($auction_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_bid WHERE auction_id = :auction_id AND deleted_at = 0');
        $stmt->execute(array('auction_id' => $auction_id));
        return $stmt;
    }

    
    public function getBanksResult()
    {
        $stmt = $this->pdo->prepare('SELECT *, CONCAT("https://www.jmviapp.com/upload_pic/",logo) as image FROM tbl_bank WHERE is_deleted = 0 ORDER BY bank_name ASC');
        $stmt->execute();
        return $stmt;
    }
    public function getLawyerResult()
    {
        $stmt = $this->pdo->prepare('SELECT *,CONCAT("https://www.jmviapp.com/upload_pic/",photo_url) as photo_url FROM tbl_lawyer WHERE is_deleted = 0 ORDER BY name ASC');
        $stmt->execute();
        return $stmt;
    }
    
    public function getRealEstateResultFavorite($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*,  IF(tbl_realestate_realestates.is_contact_price = 0 OR tbl_auction.property_id IS NOT NULL,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved,
                                        tbl_auction.id as auction_id,
                                        tbl_auction.starting_bid
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    INNER JOIN tbl_favorite_property on tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = :r_user_id
                                    LEFT JOIN tbl_auction ON tbl_auction.property_id =  tbl_favorite_property.property_id and tbl_auction.deleted_at = 0
                                    WHERE tbl_realestate_realestates.is_deleted = 0 
                                    AND  tbl_favorite_property.user_id = :user_id
                                    
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    ORDER BY tbl_favorite_property.favorite_id DESC');

        $stmt->execute(array('user_id' => $user_id, 'r_user_id' => $user_id));
        return $stmt;
    }
    
    public function getCountryResult()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_country ORDER BY country_name ASC');
        $stmt->execute();
        return $stmt;
    }
    
    public function getRealEstateResultAgentById($lat, $lon, $radius, $agent_id, $user_id = 0, $country = '')
    {
        
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, /*'radius' => $radius,*/ 'agent_id' => $agent_id);
        if (strlen($country) > 0) {
            $extraWhere .= ' AND (tbl_realestate_realestates.country LIKE :country) ';
            $bindPrams['country'] = '%' . $country . '%';            
        }
        
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND agent_id = :agent_id 
                                     '.$extraWhere.' 
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    /*HAVING distance <= :radius */
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }
    
    public function searchPropetyByCountry($params)
    {   
        $country = $params['country'];

        $sql = 'SELECT realestate_id AS total_properties ';
        
        $sql .= ' FROM tbl_realestate_realestates WHERE is_deleted = 0 ';

        if (strlen($country) > 0) {
            $sql .= 'AND (country LIKE :country) ';
            $ptr['country'] = '%' . $country . '%';            
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ptr);
        return $stmt;
    }
 
    public function getAuctionById($params)
    {
        
        $auctionId = isset($params['auction_id']) ? $params['auction_id'] : 0;
        $bindPrams['auction_id'] = $auctionId;
        
        $user_id = isset($params['user_id']) ? $params['user_id'] : 0;
        $bindPrams['user_id'] = $user_id;        
        $bindPrams['f_user_id'] = $user_id;
        $bindPrams['r_user_id'] = $user_id;
        
        $stmt = $this->pdo->prepare('SELECT ta.*,
					DATE_FORMAT(ta.start_time, "%M %d, %Y %h:%i:%s %p") as auction_start_time,
                    IFNULL(MAX(tb.bid_amount),0) as highest_bid,
                    IFNULL(MAX(tbu.bid_amount),0) as your_highest_bid,
                    IF(IFNULL(tfp.favorite_id,0) > 0, 1,0) as is_favorite,
                    IF(IFNULL(trp.id,0) > 0, 1,0) as is_reserved,
                    CONCAT(trr.currency," $") as currency,
					trr.country as country
                FROM tbl_auction as ta
                    INNER JOIN tbl_realestate_realestates as trr ON trr.realestate_id = ta.property_id
                    LEFT JOIN tbl_bid as tb ON tb.auction_id = ta.id AND tb.deleted_at = 0
                    LEFT JOIN tbl_bid as tbu ON tbu.auction_id = ta.id AND tb.deleted_at = 0 AND tbu.user_id = :user_id
                    LEFT JOIN tbl_favorite_property as tfp ON tfp.property_id = ta.property_id AND tfp.user_id = :f_user_id
                    LEFT JOIN tbl_reserved_property as trp ON trp.property_id = ta.property_id AND trp.is_deleted = 0 AND trp.user_id = :r_user_id
                    WHERE ta.deleted_at = 0 AND (ta.id = :auction_id)');
            $stmt->execute($bindPrams);
        
        return $stmt;
    }
    
    public function getPropertyById($params)
    {
        $property_id = isset($params['property_id']) ? $params['property_id'] : 0;
        $lon = isset($params['lon']) ? $params['lon'] : 0;
        $lat = isset($params['lat']) ? $params['lat'] : 0;
        $user_id = isset($params['user_id']) ? $params['user_id'] : 0;
        $extraWhere = '';
        $bindPrams = array('lat_params' => $lat, 'lon_params' => $lon, 'lat_params1' => $lat, /*'radius' => $radius,*/ 'property_id' => $property_id);
        
        
        $stmt = $this->pdo->prepare('SELECT 
                                        tbl_realestate_realestates.*, IF(tbl_realestate_realestates.is_contact_price = 0,CONCAT(tbl_realestate_realestates.currency," $"),"Contact For Price") as currency, IF(tbl_realestate_realestates.is_contact_price = 0,tbl_realestate_realestates.price,"") as price,
                                        COALESCE(( 6371 * acos( cos( radians(:lat_params) ) *  cos( radians( tbl_realestate_realestates.lat ) ) * 
                                        cos( radians( tbl_realestate_realestates.lon ) - radians(:lon_params) ) + sin( radians(:lat_params1) ) * 
                                        sin( radians( tbl_realestate_realestates.lat ) ) ) ), 0) AS distance,
                                        tbl_realestate_propertytypes.property_type AS property_type_str,
                                        IF(IFNULL(tbl_favorite_property.favorite_id,0) > 0, 1,0) as is_favorite,
                                        IF(IFNULL(tbl_reserved_property.id,0) > 0, 1,0) as is_reserved
                                            
                                    FROM tbl_realestate_realestates 
                                    LEFT JOIN tbl_realestate_propertytypes ON tbl_realestate_propertytypes.propertytype_id = tbl_realestate_realestates.property_type
                                    LEFT JOIN tbl_favorite_property ON tbl_favorite_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_favorite_property.user_id = "'.$user_id.'"
                                    LEFT JOIN tbl_reserved_property ON tbl_reserved_property.property_id = tbl_realestate_realestates.realestate_id AND tbl_reserved_property.is_deleted = 0 AND tbl_reserved_property.user_id = "'.$user_id.'"
                                    WHERE tbl_realestate_realestates.is_deleted = 0 AND tbl_realestate_realestates.realestate_id = :property_id
                                    GROUP BY tbl_realestate_realestates.realestate_id 
                                    ORDER BY distance ASC');

        $stmt->execute($bindPrams);
        return $stmt;
    }    
    
    public function getBannerResult()
    {
        $stmt = $this->pdo->prepare('SELECT *, CONCAT("https://www.jmviapp.com/upload_pic/banner/",banner_name) as banner_name FROM tbl_banner ORDER BY id ASC LIMIT 1');
        $stmt->execute();
        return $stmt;
    }    
}

?>