<?php
require_once 'DAO.php';

class Cart{
    public $account_id;
    public $listing_id;
    public $quantity;

    //画面に出す用に、JOINする列
    public $store_id;
    public $store_name;
    public $goods_name;
    public $goods_image;
    public $selling_price;
    public $stock;          //残りの在庫(listingのquantity)
    public $listing_status;
    public $sale_end;
}

class CartDAO{

    //店舗ごとカートに入れることができる上限：10点まで
    const MAX_PER_STORE = 10;

    //ユーザー用、カートの中身（店舗ごとに並べる）
    public function get_cart(int $account_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT c.account_id,c.listing_id,c.quantity,l.store_id,s.store_name,g.goods_name,g.goods_image,l.selling_price,l.quantity AS stock,l.listing_status,l.sale_end FROM cart AS c INNER JOIN listing as l ON c.listing_id = l.listing_id INNER JOIN goods_master AS g ON l.goods_id = g.goods_id INNER JOIN store AS s ON l.store_id = s.store_id WHERE c.account_id = :account_id ORDER BY l.store_id ASC,c.listing_id ASC";


        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('Cart')){
            $data[] = $row;
        }
        return $data;        
    }

    //ある店舗の商品が、カートにいくつ入っているのかを取り出す
    public function get_store_total(int $account_id,int $store_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT SUM(c.quantity) AS s FROM cart AS c INNER JOIN listing as l ON c.listing_id = l.listing_id WHERE c.account_id = :account_id AND l.store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    //カートに追加、カートになかったら新規追加する
    //10点を超えないかを計算する場合は、先にget_store_totalで確認する
    public function add(int $account_id,int $listing_id,int $quantity){
        $dbh = DAO::get_db_connect();

        $sql="SELECT COUNT(*) FROM cart WHERE account_id = :account_id AND listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->fetchColumn() > 0){

            $sql="UPDATE cart SET quantity += :quantity WHERE account_id = :account_id AND listing_id = :listing_id";

        }
        else{

            $sql="INSERT INTO cart(account_id,listing_id,quantity) VALUES(:account_id,:listing_id,:quantity)";

        }

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':quantity',$quantity,PDO::PARAM_INT);
        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);

        $stmt->execute();
    }

    //カートの数量を変える
    public function update_cart_quantity(int $account_id,int $listing_id,int $quantity){

        $dbh = DAO::get_db_connect();

        $sql="UPDATE cart SET quantity = :quantity WHERE account_id = :account_id AND listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':quantity',$quantity,PDO::PARAM_INT);
        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);

        $stmt->execute();
    }

    //カートから1行消す
    public function delete(int $account_id,int $listing_id){

        $dbh = DAO::get_db_connect();

        $sql="DELETE FROM cart WHERE account_id = :account_id AND listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);

        $stmt->execute();
    }

    //カートドロワー用・カート情報を読込
    public function read_cart(int $account_id)
    {
        $dbh = DAO::get_db_connect();

        // カートの数量・出品の価格と期限・商品名・店舗名を1回で取得！！！！！
        $sql = "SELECT l.store_id, s.store_name, g.goods_name, c.quantity AS cart_quantity, l.selling_price, l.sale_end
                FROM cart AS c
                INNER JOIN account AS a ON c.account_id = a.account_id
                INNER JOIN listing AS l ON c.listing_id = l.listing_id
                INNER JOIN goods_master AS g ON l.goods_id = g.goods_id
                INNER JOIN store AS s ON l.store_id = s.store_id
                WHERE c.account_id = :account_id
                AND a.account_type = 'user' AND a.account_status = 0
                ORDER BY l.store_id, c.listing_id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();

        $cart = ['stores' => [], 'quantity' => 0, 'total' => 0];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $storeId = (int) $row['store_id'];
            $quantity = (int) $row['cart_quantity'];
            $row['subtotal'] = (int) $row['selling_price'] * $quantity;

            // 同じ店舗の商品は、同じ配列に追加する
            if (!isset($cart['stores'][$storeId])) {
                $cart['stores'][$storeId] = [
                    'name' => $row['store_name'],
                    'items' => [],
                    'quantity' => 0,
                    'subtotal' => 0,
                ];
            }

            $cart['stores'][$storeId]['items'][] = $row;
            $cart['stores'][$storeId]['quantity'] += $quantity;
            $cart['stores'][$storeId]['subtotal'] += $row['subtotal'];
            $cart['quantity'] += $quantity;
            $cart['total'] += $row['subtotal'];
        }

        return $cart;
    }

}