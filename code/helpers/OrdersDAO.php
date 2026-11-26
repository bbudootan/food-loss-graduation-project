<?php
require_once 'DAO.php';

class Orders{
    public $order_id;
    public $account_id;
    public $store_id;
    public $order_status;
    public $total_amount;
    public $pickup_deadline;
    public $order_date;
    public $pickup_date;
    public $cancel_date;
    public $payment_date;

    //画面に出す用に、JOINする列
    public $store_name;
    public $account_name;

}

class OrderDetail{
    public $order_id;
    public $listing_id;
    public $quantity;

    public $goods_name;
    public $goods_image;
    public $selling_price;
    public $goods_changed;
}

class OrdersDAO{

    //まだ作成中
    //注文する
    //カートのうち、1つの店舗の商品を1件まとめる
    //途中で問題があったらfalseを返す
    public function create_order(int $account_id, int $store_id){
        $dbh = DAO::get_db_connect();

        try{
            $dbh->beginTransaction();

            //カートから、凍店舗の商品と今の価格を取り出す
            $sql="SELECT c.listing_id,c.quantity,l.sellint_price,l.sale_end FROM cart AS c INNER JOIN listing AS l ON c.listing_id = l.listing_id WHERE c.account_id = :account_id AND l.store_id = :store_id";

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
            $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

            $stmt->execute();

            $items = [];

            while($item = $stmt->fetch(PDO::FETCH_ASSOC)){
                $items[] = $item;
            }

            if(count($items) === 0){
                $dbh->rollBack();
                return false;
            }


            //支払額を計算する
            $total = 0;
            foreach($items as $item){
                $total += $item['selling_price'] * $item['quantity']; 
            }

            //受け取り締め切り日時を決める
            //
            $pickup_deadline = $this->get_pickup_deadline($dbh,$store_id,$items);


            //注文を登録する
            $sql="INSERT INTO orders(account_id,store_id,total_amount,pickup_deadline,order_date,payment_date) VALUES(:account_id,:store_id,:total_amount,:pickup_deadline,GETDATE(),GETDATE())";

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
            $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
            $stmt->bindValue(':total_amount',$total,PDO::PARAM_INT);
            $stmt->bindValue(':pickup_deadline',$pickup_deadline,PDO::PARAM_STR);

            $stmt->execute();
            $order_id = $this->get_order_id($dbh);

            foreach($items as $item){
                //在庫を減らす
                $sql="UPDATE listing SET quantity -= :quantity WHERE listing_id = :listing_id AND quantity >= :quantity2 AND listing_status = 1 AND sale_end > GETDATE()";

                $stmt = $dbh->prepare($sql);

                $stmt->bindValue(':quantity',$item['quantity'],PDO::PARAM_INT);
                $stmt->bindValue(':quantity2',$item['quantity'],PDO::PARAM_INT);
                $stmt->bindValue(':listing_id',$item['listing_id'],PDO::PARAM_INT);

                $stmt->execute();

                if($stmt->rowCount() !== 1){
                    $dbh->rollBack();
                    return false;
                }

                //注文明細への登録
                $sql="INSERT INTO order_detail(order_id,listing_id,quantity) VALUES(:order_id,:listing_id,:quantity)";

                $stmt = $dbh->prepare($sql);

                $stmt->bindValue(':order_id',$order_id,PDO::PARAM_INT);
                $stmt->bindValue(':quantity',$item['quantity'],PDO::PARAM_INT);
                $stmt->bindValue(':listing_id',$item['listing_id'],PDO::PARAM_INT);

                $stmt->execute();
            }

            //注文した店舗の商品をカートから削除する
            $sql="DELETE FROM cart AS c INNER JOIN listing AS l ON c.listing_id = l.listing_id WHERE c.account_id = :account_id AND l.store_id = :store_id";

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
            $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

            $stmt->execute();

            $dbh->commit();

            return $order_id;
        }

        catch(PDOException $e){
            $dbh->rollBack();
        }

    }

    private function get_order_id($dbh){

        $sql = "SELECT IDENT_CURRENT('order') AS order_id";

        $stmt = $dbh->query($sql);

        $row = $stmt->fetchObject();

        return $row->order_id;
    }

    private function get_pickup_deadline($dbh,int $store_id,array $items){
        
        $sql = "SELECT closint_time FROM store WHERE store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

        $stmt->execute();
        $closint_time = $stmt->fetchColumn();

        $deadline = strtotime(date('Y-m-d')." ".substr($closint_time,0,8));

        foreach($items as $item){
            $sale_end = strtotime(substr($item['sale_end'], 0, 19));
            if($sale_end < $deadline){
                $deadline = $sale_end;
            }
        }

        return date('Y-m-d H:i:s', $deadline);

    }
}