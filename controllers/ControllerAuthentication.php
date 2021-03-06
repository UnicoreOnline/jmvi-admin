<?php

require_once '/var/www/html/project_new/models/Authentication.php';
require_once "/var/www/html/project_new/PHPMailer/PHPMailerAutoload.php";
 
class ControllerAuthentication
{
    private $db;
    private $pdo;
    function __construct() 
    {
        require_once '/var/www/html/project_new/application/DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->pdo = $this->db->connect();

    }
 
    function __destruct() 
    {
         
    }

    
    public function login($username, $password) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_authentication 
                                WHERE username = :username AND password = :password AND deny_access = 0 AND is_deleted = 0');

        $stmt->execute(
            array('username' => $username, 
                    'password' => $password) );

        foreach ($stmt as $row) 
        {
            // do something with $row
            $auth = new Authentication();
            $auth->authentication_id = $row['authentication_id'];
            $auth->username = $row['username'];
            $auth->password = $row['password'];
            $auth->name = $row['name'];
            $auth->role_id = $row['role_id'];

            return $auth;
        }

        return null;

    }

    public function insertAccessUser($itm) 
    {
        $stmt = $this->pdo->prepare('INSERT INTO tbl_realestate_authentication( username, password, name, role_id ) 
                                        VALUES( :username, :password, :name, :role_id )');
        
        $result = $stmt->execute(
                            array('username' => $itm->username,
                                    'password' => $itm->password,
                                    'name' => $itm->name,
                                    'role_id' => 2) );
        
        return $result ? true : false;
    }

    public function updateAccessUser($itm) 
    {
        
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_authentication 
                                        SET 
                                            username = :username,
                                            password = :password,
                                            name = :name

                                        WHERE authentication_id = :authentication_id');

        $result = $stmt->execute(
                            array('username' => $itm->username,
                                    'password' => $itm->password,
                                    'name' => $itm->name,
                                    'authentication_id' => $itm->authentication_id) );
        
        return $result ? true : false;
    }

    public function deleteAccessUser($authentication_id, $is_deleted) 
    {

        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_authentication 
                                        SET is_deleted = :is_deleted
                                        WHERE authentication_id = :authentication_id');


        $result = $stmt->execute(
                            array('is_deleted' => $is_deleted, 
                                    'authentication_id' => $authentication_id) );
        
        return $result ? true : false;
    }

    public function getAccessUser() 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_authentication 
                                 WHERE is_deleted = 0 ORDER BY name ASC');

        $stmt->execute();

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new Authentication();
            $itm->authentication_id = $row['authentication_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->name = $row['name'];
            $itm->role_id = $row['role_id'];
            $itm->is_deleted = $row['is_deleted'];
            $itm->deny_access = $row['deny_access'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function getAccessUserByAuthenticationId($authentication_id) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_authentication 
                                 WHERE authentication_id = :authentication_id ');

        $result = $stmt->execute(
                            array('authentication_id' => $authentication_id) );

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new Authentication();
            $itm->authentication_id = $row['authentication_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->name = $row['name'];
            $itm->role_id = $row['role_id'];
            $itm->is_deleted = $row['is_deleted'];
            $itm->deny_access = $row['deny_access'];

            return $itm;
        }
        return null;
    }

    public function checkUsername($username) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                FROM tbl_realestate_authentication 
                                 WHERE username = :username ');

        $result = $stmt->execute(
                            array('username' => $username) );

        foreach ($stmt as $row) 
        {
            // do something with $row
            
            return true;
        }
        return false;
    }

    public function getAccessUsersBySearching($search) 
    {
        $stmt = $this->pdo->prepare('SELECT * 
                                        FROM tbl_realestate_authentication 
                                        WHERE is_deleted = 0 AND name LIKE :search ORDER BY name ASC');

        $stmt->execute( array('search' => '%'.$search.'%'));

        $array = array();
        $ind = 0;
        foreach ($stmt as $row) 
        {
            // do something with $row
            $itm = new Authentication();
            $itm->authentication_id = $row['authentication_id'];
            $itm->username = $row['username'];
            $itm->password = $row['password'];
            $itm->name = $row['name'];
            $itm->role_id = $row['role_id'];
            $itm->is_deleted = $row['is_deleted'];
            $itm->deny_access = $row['deny_access'];

            $array[$ind] = $itm;
            $ind++;
        }
        return $array;
    }

    public function denyUserAccess($authentication_id, $deny_access) 
    {
        $stmt = $this->pdo->prepare('UPDATE tbl_realestate_authentication 
                                        SET deny_access = :deny_access 
                                        WHERE authentication_id = :authentication_id');

        $result = $stmt->execute(
                            array('deny_access' => $deny_access, 
                                    'authentication_id' => $authentication_id) );
        
        return $result ? true : false;
    }
	
	public function resetPassword($username)
	{
		$senderName = "JMVI RealEstate";
		$to = "jmviapp@gmail.com";
		$password = $this->generateRandomString();
		
		$stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_authentication WHERE username = :username limit 1');
        $result = $stmt->execute(array('username' => $username));
		
		foreach ($stmt as $row) {
			if(!empty($row["username"])) {
					
				$stmtu 	 = $this->pdo->prepare('UPDATE tbl_realestate_authentication SET password = :password WHERE username = :username');
				$resultu = $stmtu->execute(array('password' => md5($password),'username' => $username) );	
				$mail = new PHPMailer(true);
				$mail->isSMTP();
				$mail->Host = "smtp.gmail.com";
				$mail->SMTPAuth = true;
				$mail->Username = $to;
				$mail->Password = "investmentjmvi";
				$mail->SMTPSecure = "ssl";
				$mail->Port = 465;
				$mail->From = $to;
				$mail->FromName = $senderName;
				$mail->addAddress("investmentjmvi@qq.com", $senderName);
				$mail->isHTML(true);
				$mail->Subject = "Your password has been reset";
				$mail->Body = "Your password has been reset. To sign back use {$password}";
				try {
                    if (!$mail->send()) {
                        
                    }
                } catch (Exception $e) {
                    
                }
                
				return true;
			}
		}

		return false;	
		
	}

        public function resetPasswordByEmail($email)
	{
		$senderName = "JMVI RealEstate";
        $to = "jmviapp@gmail.com";
		$password = $this->generateRandomString();
		
		$stmt = $this->pdo->prepare('SELECT * FROM tbl_realestate_users WHERE email = :email limit 1');
        $result = $stmt->execute(array('email' => $email));
		
		foreach ($stmt as $row) {
			if(!empty($row["email"])) {
					
				$stmtu 	 = $this->pdo->prepare('UPDATE tbl_realestate_users SET password = :password WHERE email = :email');
				$resultu = $stmtu->execute(array('password' => md5($password),'email' => $email) );	
				$mail = new PHPMailer(true);
				$mail->isSMTP();
				$mail->Host = "smtp.gmail.com";
				$mail->SMTPAuth = true;
				$mail->Username = $to;
				$mail->Password = "investmentjmvi";
				$mail->SMTPSecure = "ssl";
				$mail->Port = 465;
				$mail->From = $to;
				$mail->FromName = $senderName;
				$mail->addAddress($row["email"]);
				$mail->isHTML(true);
				$mail->Subject = "Your password has been reset";
				$mail->Body = "Your password has been reset. To sign back use {$password}";
				try {
                    if (!$mail->send()) {
                        
                    }
                } catch (Exception $e) {
                    
                }
                
				return true;
			}
		}

		return false;	
		
	}
        
	public function generateRandomString($length = 7) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	

}
 
?>