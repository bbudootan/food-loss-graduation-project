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
    public $updated_date;
    public $order_date;
}

class OrdersDAO{

    //注文する
    //カートのうち、1つの店舗の商品を1件まとめる
    //途中で問題があったらfalseを返す
    public function create_order(int $account_id, int $store_id){
        $dbh = DAO::get_db_connect();

        try{
            $dbh->beginTransaction();

            //カートから、凍店舗の商品と今の価格を取り出す
            $sql="SELECT c.listing_id,c.quantity,l.selling_price,l.sale_end FROM cart AS c INNER JOIN listing AS l ON c.listing_id = l.listing_id WHERE c.account_id = :account_id AND l.store_id = :store_id";

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
            $pickup_deadline = $this->make_pickup_deadline($dbh,$store_id,$items);


            //注文を登録する
            $sql="INSERT INTO orders(account_id,store_id,total_amount,pickup_deadline,order_date,payment_date) VALUES(:account_id,:store_id,:total_amount,:pickup_deadline,GETDATE(),GETDATE())";

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
            $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
            $stmt->bindValue(':total_amount',$total,PDO::PARAM_INT);
            $stmt->bindValue(':pickup_deadline',$pickup_deadline,PDO::PARAM_STR);

            $stmt->execute();
            $order_id = (int)$dbh->lastInsertId();

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
            $sql="DELETE c FROM cart AS c INNER JOIN listing AS l ON c.listing_id = l.listing_id WHERE c.account_id = :account_id AND l.store_id = :store_id";

            $stmt = $dbh->prepare($sql);

            $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
            $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

            $stmt->execute();

            $dbh->commit();

            return $order_id;
        }

        catch(PDOException $e){
            $dbh->rollBack();
            return false;
        }

    }

    // 注文確認画面、注文する前に、受け取り締め切り日時だけを計算して返す
    // 「19:00まで受取可」のように表示するために使う。カートにその店舗の商品がなければ false
    public function get_pickup_deadline(int $account_id, int $store_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT c.listing_id, l.sale_end FROM cart AS c INNER JOIN listing AS l ON c.listing_id = l.listing_id WHERE c.account_id = :account_id AND l.store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_INT);

        $stmt->execute();

        $items = [];

        while($item = $stmt->fetch(PDO::FETCH_ASSOC)){
            $items[] = $item;
        }

        if (count($items) === 0) {
            return false;
        }

        return $this->make_pickup_deadline($dbh, $store_id, $items);
    }


    //受け取り締め切り日時を作る
    // 今日の日付＋店舗の閉店時間 と、カートの商品の sale_end のうちいちばん早いもの を比べて、早いほうにする
    private function make_pickup_deadline($dbh,int $store_id,array $items){
        
        $sql = "SELECT closing_time FROM store WHERE store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

        $stmt->execute();
        $closing_time = $stmt->fetchColumn();

        $deadline = strtotime(date('Y-m-d')." ".substr($closing_time,0,8));

        if($deadline < time()){
            $deadline = strtotime('+1 day',$deadline);
        }

        foreach($items as $item){
            $sale_end = strtotime(substr($item['sale_end'], 0, 19));
            if($sale_end < $deadline){
                $deadline = $sale_end;
            }
        }

        return date('Y-m-d H:i:s', $deadline);

    }

    //ユーザー側

    // 注文履歴（新しい順）
    public function get_orders_by_account(int $account_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT o.*, s.store_name FROM orders AS o INNER JOIN store AS s ON o.store_id = s.store_id WHERE o.account_id = :account_id ORDER BY o.order_date DESC";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('Orders')){
            $data[] = $row;
        }
        return $data;
    }

    // 注文完了・注文詳細：自分の注文を1件
    public function get_order_for_account(int $order_id, int $account_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT o.*, s.store_name FROM orders AS o INNER JOIN store AS s ON o.store_id = s.store_id WHERE o.order_id = :order_id AND o.account_id = :account_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchObject('Orders');
    }

    // 店舗の注文管理・受け取り：注文を1件、ほかの店舗の注文なら false
    // 受け取りでは、受け取り確認コード「店舗ID-注文ID」を分けた注文IDで呼ぶ。
    // 注文者のアカウント名も返す（余裕があれば受け取り画面に「注文者：○○様」と出す。）
    public function get_order_for_store(int $order_id, int $store_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT o.*, s.store_name, a.account_name FROM orders AS o INNER JOIN store AS s ON o.store_id = s.store_id INNER JOIN account AS a ON o.account_id = a.account_id WHERE o.order_id = :order_id AND o.store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchObject('Orders');
    }

    // 注文明細、ユーザーの注文詳細・店舗の注文管理／受け取り
    // goods_changed、注文日時より後に商品マスターが編集されていたら1
    public function get_order_details(int $order_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT d.order_id, d.listing_id, d.quantity, g.goods_name, g.goods_image, l.selling_price, g.updated_date, o.order_date FROM order_detail AS d INNER JOIN orders AS o ON d.order_id = o.order_id INNER JOIN listing AS l ON d.listing_id = l.listing_id INNER JOIN goods_master AS g ON l.goods_id = g.goods_id WHERE d.order_id = :order_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('OrderDetail')){
            $row->goods_changed = 0;

            if($row->updated_date !== null){

                if(strtotime($row->updated_date) > strtotime($row->order_date)){

                    $row->goods_changed = 1;
                }

            }

            $data[] = $row;
        }

        return $data;
    }

    //店舗側

    // 注文管理：自分の店舗の注文（状態で絞り込み。-1なら全部）
    public function get_orders_by_store(int $store_id, int $order_status = -1){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT o.*, a.account_name FROM orders AS o INNER JOIN account AS a ON o.account_id = a.account_id WHERE o.store_id = :store_id";

        if ($order_status >= 0) {
            $sql .= " AND o.order_status = :order_status";
        }

        $sql .= " ORDER BY o.pickup_deadline";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_INT);

        if ($order_status >= 0) {
            $stmt->bindValue(':order_status', $order_status, PDO::PARAM_INT);
        }

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('Orders')){
            $data[] = $row;
        }
        return $data;
    }

    // 受け取り完了にする。受け取り待ち（0）の注文だけ変える
    // 変わったら true、もう受け取り済みなどで変わらなかったら false
    public function complete_pickup(int $order_id, int $store_id){
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE orders SET order_status = 1, pickup_date = GETDATE() WHERE order_id = :order_id AND store_id = :store_id AND order_status = 0";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->rowCount() === 1){
            return true;
        }
        else{
            return false;
        }

    }

    // 在庫調整：その出品の、受け取り待ちの注文に入っている数の合計（まだ渡していない数）
    // 注文がなければ 0
    public function get_waiting_quantity(int $listing_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT SUM(d.quantity) FROM order_detail AS d INNER JOIN orders AS o ON d.order_id = o.order_id WHERE d.listing_id = :listing_id AND o.order_status = 0";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':listing_id', $listing_id, PDO::PARAM_INT);

        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    // 店舗の注文管理の「キャンセル」、受け取り待ちの注文だけを「3=キャンセル」にする
    // 自分の店舗の注文だけ変えられる。在庫は戻さない
    // 画面では order_status が3なら「店舗都合によりキャンセルされました」と出す
    // 変わったら true、もう受け取り済み・キャンセル済みで変わらなかったら false
    public function cancel_order(int $order_id, int $store_id){
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE orders SET order_status = 3, cancel_date = GETDATE() WHERE order_id = :order_id AND store_id = :store_id AND order_status = 0";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() === 1;
    }
}