<?php
require_once 'DAO.php';

class Orders{
    public $order_id;
    public $account_id;
    public $store_id;
    public $order_status;
    public $total_amount;
    public $pickup_schedule;
    public $order_date;
    public $pickup_date;
    public $cancel_date;
    public $payment_date;
}

class OrdersDAO{

}