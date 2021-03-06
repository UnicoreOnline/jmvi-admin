<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class ControllerUser
{

    private $db;
    private $db_path;
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

    public function registerUser($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_realestate_users( 
                                        full_name,
                                        username, 
                                        password, 
                                        login_hash, 
                                        facebook_id,
                                        twitter_id,
                                        mobile,
                                        address,
                                        country,
                                        email,
                                        facebook_url,
                                        twitter_url,
                                        apple_id
                                        ) 
                                    VALUES( 
                                        :full_name,
                                        :username,
                                        :password,
                                        :login_hash,
                                        :facebook_id,
                                        :twitter_id,
                                        :mobile,
                                        :address,
                                        :country,
                                        :email,
                                        :facebook_url,
                                        :twitter_url,
                                        :apple_id)');

        $result = $stmt->execute(
            array('full_name' => $itm->full_name,
                'username' => $itm->username,
                'password' => $itm->password,
                'login_hash' => $this->hashSSHA($itm->login_hash),
                'facebook_id' => $itm->facebook_id,
                'twitter_id' => $itm->twitter_id,
                'mobile' => $itm->mobile,
                'address' => $itm->address,
                'country' => isset($itm->country) ? $itm->country : "",
                'email' => $itm->email,
                'facebook_url' => $itm->facebook_url,
                'twitter_url' => $itm->twitter_url,
                'apple_id' => $itm->apple_id
                ));        
        

        return $result ? true : false;
    }

    public function loginUser($username, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users WHERE (username = :username or email = :email) AND password = :password AND deny_access = 0 and is_deleted != 1');

        $result = $stmt->execute(
            array('username' => $username,
				'email' => $username,
                'password' => $password));

        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $this->hashSSHA($row['password']);
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->full_name = $row['full_name'];
            $itm->mobile = $row['mobile'];
            $itm->address = $row['address'];
            $itm->country = $row['country'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];            

            return $itm;
        }

        return null;
    }


    public function loginFacebook($facebook_id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_users 
                                 WHERE facebook_id = :facebook_id AND deny_access = 0');

        $result = $stmt->execute(
            array('facebook_id' => $facebook_id));

        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $this->hashSSHA($row['password']);
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->full_name = $row['full_name'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];

            return $itm;
        }

        return null;
    }


    public function loginTwitter($twitter_id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_users 
                                 WHERE twitter_id = :twitter_id AND deny_access = 0');

        $result = $stmt->execute(
            array('twitter_id' => $twitter_id));

        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $this->hashSSHA($row['password']);
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->full_name = $row['full_name'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];

            return $itm;
        }

        return null;
    }


    public function getUserByUserId($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_users 
                                 WHERE user_id = :user_id');

        $result = $stmt->execute(
            array('user_id' => $user_id));

        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $row['login_hash'];
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->address = $row['address'];
            $itm->mobile = $row['mobile'];
            $itm->country = $row['country'];
            $itm->full_name = $row['full_name'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];
            
            return $itm;
        }

        return null;
    }


    public function changePassword($user_id, $password)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_users 
                                        SET password = :password 
                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
            array('password' => $password,
                'user_id' => $user_id));

        return $result ? true : false;
    }

    public function updateUserHash($itm)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_users 
                                        SET login_hash = :login_hash 
                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
            array('login_hash' => $itm->login_hash,
                'user_id' => $itm->user_id));

        return $result ? true : false;
    }

    public function isUserExist($username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users 
                                        WHERE username = :username');

        $result = $stmt->execute(
            array('username' => $username));

        foreach ($stmt as $row) {
            return true;
        }

        return false;
    }

    public function isEmailExist($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users 
                                        WHERE email = :email');

        $result = $stmt->execute(
            array('email' => $email));

        foreach ($stmt as $row) {
            return true;
        }

        return false;
    }

    public function isFacebookIdExist($facebook_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users 
                                        WHERE facebook_id = :facebook_id');

        $result = $stmt->execute(
            array('facebook_id' => $facebook_id));

        foreach ($stmt as $row) {
            return true;
        }

        return false;
    }

    public function isTwitterIdExist($twitter_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users 
                                        WHERE twitter_id = :twitter_id');

        $result = $stmt->execute(
            array('twitter_id' => $twitter_id));

        foreach ($stmt as $row) {
            return true;
        }

        return false;
    }


    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password)
    {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        // $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $encrypted;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password)
    {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

    public function getUsers()
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_users WHERE is_deleted = 0 ORDER BY user_id DESC');

        $result = $stmt->execute();

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $row['login_hash'];
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->full_name = $row['full_name'];
            $itm->deny_access = $row['deny_access'];
            $itm->mobile = $row['mobile'];
            $itm->address = $row['address'];
            $itm->country = $row['country'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];

            $array[$ind] = $itm;
            $ind++;
        }

        return $array;
    }

    public function updateUserAccess($user_id, $deny_access)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_users 
                                        SET deny_access = :deny_access 
                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
            array('deny_access' => $deny_access,
                'user_id' => $user_id));

        return $result ? true : false;
    }

    public function getUsersBySearching($search)
    {
        $stmt = $this->pdo->prepare("SELECT * 
                                        FROM tbl_realestate_users 
                                        WHERE deny_access = 0 AND full_name LIKE :search ORDER BY full_name ASC");

        $stmt->execute(array('search' => '%' . $search . '%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $row['login_hash'];
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->mobile = $row['mobile'];
            $itm->address = $row['address'];
            $itm->country = $row['country'];
            $itm->full_name = $row['full_name'];
            $itm->deny_access = $row['deny_access'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function isUserValid($user_id, $login_hash)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users WHERE user_id = :user_id AND login_hash = :login_hash AND deny_access = 0');
        $stmt->execute(array('user_id' => $user_id, 'login_hash' => $login_hash));
        return $stmt->rowCount() == 1 ? true : false;
    }

    public function getResultUser($user_id, $login_hash)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users WHERE user_id = :user_id AND login_hash = :login_hash AND deny_access = 0');
        $stmt->execute(array('username' => $username));
        return $stmt->rowCount() == 1 ? true : false;
    }

    public function deleteUser($user_id, $is_deleted)
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_users 
                                        SET is_deleted = :is_deleted , email = "" , username = "" 
                                        WHERE user_id = :user_id ');

        $result = $stmt->execute(
            array(
                'is_deleted' => $is_deleted,
                'user_id' => $user_id
            )
        );

        return $result ? true : false;
    }
	
	public function updateUser($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_users
                                        SET 
											email = :email,
                                            full_name = :full_name, 
                                            username = :username, 
                                            mobile = :mobile, 
                                            address = :address, 
                                            country = :country,
					    password = :password,
					    twitter_url = :twitter_url,
					    facebook_url = :facebook_url

                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
                            array('full_name' => $itm->full_name,
                                    'username' => $itm->username,
                                    'mobile' => $itm->mobile,
                                    'address' => $itm->address,
                                    'country' => $itm->country,
                                    'user_id' => $itm->user_id,
                                    'password' => $itm->password,
                                    'twitter_url' => $itm->twitter_url,
                                    'facebook_url' => $itm->facebook_url,
                                    'email' => $itm->email
								) );
        
        
        return $result ? true : false;
    }


    public function isActive($email , $user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_users 
                                 WHERE user_id = :user_id and email = :email and deny_access = 0 and is_deleted != 1');

        $result = $stmt->execute(
            array(
                'user_id' => $user_id,
                'email' => $email
        ));

        return $stmt->rowCount() == 1 ? true : false;
    }
    
    public function isAppleIdExist($apple_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users 
                                        WHERE apple_id = :apple_id');

        $result = $stmt->execute(
            array('apple_id' => $apple_id));

        foreach ($stmt as $row) {
            return true;
        }

        return false;
    }
    
    public function loginApple($apple_id)
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_users 
                                 WHERE apple_id = :apple_id AND deny_access = 0');

        $result = $stmt->execute(
            array('apple_id' => $apple_id));

        foreach ($stmt as $row) {
            // do something with $row
            $itm = new User();
            $itm->user_id = $row['user_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->login_hash = $this->hashSSHA($row['password']);
            $itm->facebook_id = $row['facebook_id'];
            $itm->twitter_id = $row['twitter_id'];
            $itm->email = $row['email'];
            $itm->full_name = $row['full_name'];
            $itm->facebook_url = $row['facebook_url'];
            $itm->twitter_url = $row['twitter_url'];
            $itm->apple_id = $row['apple_id'];
            $itm->address = $row['address'];
            $itm->country = $row['country'];
            $itm->mobile = $row['mobile'];

            return $itm;
        }

        return null;
    }

    public function updateProfile($itm) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_users
                                        SET 
                                            full_name = :full_name, 
                                            username = :username, 
                                            mobile = :mobile, 
                                            address = :address, 
                                            country = :country,
                                            email = :email

                                        WHERE user_id = :user_id');

        $result = $stmt->execute(
                            array('full_name' => $itm->full_name,
                                    'username' => $itm->username,
                                    'mobile' => $itm->mobile,
                                    'address' => $itm->address,
                                    'country' => $itm->country,
                                    'user_id' => $itm->user_id,
                                    'email' => $itm->email
                                ) );
        
        return $result ? true : false;
    }
}

?>