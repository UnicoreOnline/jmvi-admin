<?php
 
class ControllerAgent
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
 
    public function updateAgent($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_agents
                                        SET 
                                            address = :address, 
                                            contact_no = :contact_no, 
                                            country = :country, 
                                            updated_at = :updated_at, 
                                            email = :email, 
                                            name = :name, 
                                            sms = :sms, 
                                            zipcode = :zipcode, 
                                            photo_url = :photo_url, 
                                            thumb_url = :thumb_url, 
                                            twitter = :twitter, 
                                            fb = :fb, 
                                            linkedin = :linkedin, 
                                            company = :company, 
                                            website = :website

                                        WHERE agent_id = :agent_id');

        $result = $stmt->execute(
                            array('address' => $itm->address,
                                    'contact_no' => $itm->contact_no,
                                    'country' => $itm->country,
                                    'updated_at' => $itm->updated_at,
                                    'email' => $itm->email,
                                    'name' => $itm->name,
                                    'sms' => $itm->sms,
                                    'zipcode' => $itm->zipcode,
                                    'photo_url' => $itm->photo_url,
                                    'thumb_url' => $itm->thumb_url,
                                    'twitter' => $itm->twitter,
                                    'fb' => $itm->fb,
                                    'linkedin' => $itm->linkedin,
                                    'company' => $itm->company,
                                    'website' => $itm->website,
                                    'agent_id' => $itm->agent_id) );
        
        return $result ? true : false;
    }

    public function deleteAgent($agent_id, $is_deleted) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_agents 
                                        SET 
                                            is_deleted = :is_deleted 

                                        WHERE agent_id = :agent_id');

        $result = $stmt->execute(
                            array('is_deleted' => $is_deleted,
                                    'agent_id' => $agent_id) );
        
        return $result ? true : false;
    }

    public function insertAgent($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_realestate_agents( 
                                        address, 
                                        contact_no, 
                                        country, 
                                        updated_at, 
                                        created_at, 
                                        email, 
                                        name, 
                                        sms, 
                                        zipcode, 
                                        photo_url, 
                                        thumb_url, 
                                        twitter, 
                                        fb, 
                                        linkedin, 
                                        company,
                                        website,  
                                        user_id ) 

                                    VALUES(
                                        :address, 
                                        :contact_no, 
                                        :country, 
                                        :updated_at, 
                                        :created_at, 
                                        :email, 
                                        :name, 
                                        :sms, 
                                        :zipcode, 
                                        :photo_url, 
                                        :thumb_url, 
                                        :twitter, 
                                        :fb, 
                                        :linkedin, 
                                        :company,
                                        :website,
                                        :user_id )');

        $result = $stmt->execute(
                            array('address' => $itm->address,
                                    'contact_no' => $itm->contact_no,
                                    'country' => $itm->country,
                                    'updated_at' => $itm->updated_at,
                                    'email' => $itm->email,
                                    'name' => $itm->name,
                                    'sms' => $itm->sms,
                                    'zipcode' => $itm->zipcode,
                                    'photo_url' => $itm->photo_url,
                                    'thumb_url' => $itm->thumb_url,
                                    'twitter' => $itm->twitter,
                                    'fb' => $itm->fb,
                                    'linkedin' => $itm->linkedin,
                                    'company' => $itm->company,
                                    'website' => $itm->website,
                                    'created_at' => $itm->created_at,
                                    'user_id' => $itm->user_id) );
        
        return $result ? true : false;

    }
 
    
    public function getAgents() 
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_realestate_agents 
                                        WHERE is_deleted = 0 ORDER BY name ASC");

        $result = $stmt->execute( );

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new Agent();
            $itm->address = $row['address'];
            $itm->agent_id = $row['agent_id'];
            $itm->contact_no = $row['contact_no'];
            $itm->country = $row['country'];
            $itm->created_at = $row['created_at'];
            $itm->email = $row['email'];
            $itm->name = $row['name'];
            $itm->sms = $row['sms'];
            $itm->updated_at = $row['updated_at'];
            $itm->zipcode = $row['zipcode'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->twitter = $row['twitter'];
            $itm->fb = $row['fb'];
            $itm->linkedin = $row['linkedin'];
            $itm->company = $row['company'];
            $itm->user_id = $row['user_id'];
            $itm->website = $row['website'];

            $array[$ind] = $itm;
            $ind++;
        }

        return $array;
    }

    public function getAgentsBySearching($search) 
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_realestate_agents 
                                        WHERE is_deleted = 0 AND name LIKE :search ORDER BY name ASC");

        $stmt->execute( array('search' => '%'.$search.'%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
        
            $itm = new Agent();
            $itm->address = $row['address'];
            $itm->agent_id = $row['agent_id'];
            $itm->contact_no = $row['contact_no'];
            $itm->country = $row['country'];
            $itm->created_at = $row['created_at'];
            $itm->email = $row['email'];
            $itm->name = $row['name'];
            $itm->sms = $row['sms'];
            $itm->updated_at = $row['updated_at'];
            $itm->zipcode = $row['zipcode'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->twitter = $row['twitter'];
            $itm->fb = $row['fb'];
            $itm->linkedin = $row['linkedin'];
            $itm->company = $row['company'];
            $itm->user_id = $row['user_id'];
            $itm->website = $row['website'];
            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }


    public function getAgentByAgentId($agent_id) 
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_realestate_agents 
                                        WHERE agent_id = :agent_id");

        $stmt->execute( array('agent_id' => $agent_id));
        foreach ($stmt as $row) 
        {
            $itm = new Agent();
            $itm->address = $row['address'];
            $itm->agent_id = $row['agent_id'];
            $itm->contact_no = $row['contact_no'];
            $itm->country = $row['country'];
            $itm->created_at = $row['created_at'];
            $itm->email = $row['email'];
            $itm->name = $row['name'];
            $itm->sms = $row['sms'];
            $itm->updated_at = $row['updated_at'];
            $itm->zipcode = $row['zipcode'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->twitter = $row['twitter'];
            $itm->fb = $row['fb'];
            $itm->linkedin = $row['linkedin'];
            $itm->company = $row['company'];
            $itm->user_id = $row['user_id'];
            $itm->website = $row['website'];
            return $itm;
        }
        return null;
    }

    public function getAgentByUserId($user_id) 
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_realestate_agents 
                                        WHERE user_id = :user_id AND is_deleted = 0 ");

        $stmt->execute( array('user_id' => $user_id));
        foreach ($stmt as $row) 
        {
            $itm = new Agent();
            $itm->address = $row['address'];
            $itm->agent_id = $row['agent_id'];
            $itm->contact_no = $row['contact_no'];
            $itm->country = $row['country'];
            $itm->created_at = $row['created_at'];
            $itm->email = $row['email'];
            $itm->name = $row['name'];
            $itm->sms = $row['sms'];
            $itm->updated_at = $row['updated_at'];
            $itm->zipcode = $row['zipcode'];
            $itm->photo_url = $row['photo_url'];
            $itm->thumb_url = $row['thumb_url'];
            $itm->twitter = $row['twitter'];
            $itm->fb = $row['fb'];
            $itm->linkedin = $row['linkedin'];
            $itm->company = $row['company'];
            $itm->user_id = $row['user_id'];
            $itm->website = $row['website'];
            return $itm;
        }
        return null;
    }


    public function updateAgentPhotos($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_agents
                                        SET 
                                            photo_url = :photo_url, 
                                            thumb_url = :thumb_url 

                                        WHERE agent_id = :agent_id');

        $result = $stmt->execute(
                            array(
                                    'photo_url' => $itm->photo_url,
                                    'thumb_url' => $itm->thumb_url,
                                    'agent_id' => $itm->agent_id) );
        
        return $result ? true : false;
    }

    public function getLastInsertedId() {

        return $this->pdo->lastInsertId(); 
    }


}
 
?>