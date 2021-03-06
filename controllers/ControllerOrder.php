<?php
/**
 * Created by PhpStorm.
 * User: bluefox
 * Date: 8/2/18
 * Time: 11:57 PM
 */

class ControllerOrder
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

    public function insertOrder($itm)
    {
        $stmt = $this->pdo->prepare('INSERT INTO 
                                        tbl_order( user_id, reserverd_property_id, transaction_amount, transaction_id, transaction_status, transaction_response, created_at )
                                        VALUES
                                        ( :user_id, :reserverd_property_id, :transaction_amount, :transaction_id, :transaction_status, :transaction_response,:created_at )');

        $result = $stmt->execute(
            array(
                ':user_id' => $itm->user_id,
                ':reserverd_property_id' => $itm->reserverd_property_id,
                ':transaction_amount' => $itm->transaction_amount,
                ':transaction_id' => $itm->transaction_id,
                ':transaction_status' => $itm->transaction_status,
                ':transaction_response' => $itm->transaction_response,
                ':created_at' => $itm->created_at));

        return $result ? true : false;
    }

    public function getLastInsertedId()
    {
        $query = $this->pdo->prepare('SELECT *  FROM tbl_order order by order_id DESC LIMIT 1');
        $query->execute();
        $row = $query->fetch();
        return $row;
    }

    public function formatBank($row)
    {
        $itm = new Order();
        $itm->order_id = $row['order_id'];
        $itm->user_id = $row['user_id'];
        $itm->reserverd_property_id = $row['reserverd_property_id'];
        $itm->transaction_amount = $row['transaction_amount'];
        $itm->transaction_id = $row['transaction_id'];
        $itm->transaction_status = $row['transaction_status'];
        $itm->transaction_response = $row['transaction_response'];
        $itm->created_at = $row['created_at'];        
        return $itm;
    }
}