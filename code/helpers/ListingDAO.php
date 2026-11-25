<?php
require_once 'DAO.php';

class Listing{
    public $listing_id;
    public $store_id;
    public $goods_id;
    public $regular_price;
    public $selling_price;
    public $quantity;
    public $food_deadline;
    public $listing_status;
    public $recommend;
    public $sale_end;
    public $created_date;

    //画面に出す用に、JOINする列
    public $goods_name;
    public $goods_image;
    public $unit_weight;
    public $allergen;
    public $goods_description;
    public $storage_method;
    public $category_id;
    public $store_name;
}

class ListingDAO{

    //ユーザー用、出品中でかつ、在庫があるかつ、利用可能な店舗の出品情報を取得
    public function get_listing_for_user(){
        $dbh = DAO::get_db_connect();

        $sql="SELECT l.*,g.goods_name,g.goods_image,g.unit_weight,g.allergen,g.goods_description,g.storage_method,g.category_id,s.store_name FROM listing AS l INNER JOIN goods_master AS g ON l.goods_id = g.goods_id INNER JOIN store AS s ON l.store_id = s.store_id WHERE l.listing_status = 1 AND l.quantity > 0 AND l.sale_end > GETDATE() AND s.store_status = 0";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('Listing')){
            $data[] = $row;
        }
        return $data;
    }

    //店舗詳細用、その店舗の今買える出品
    public function get_listing_for_user_by_store(int $store_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT l.*,g.goods_name,g.goods_image,g.unit_weight,g.allergen,g.goods_description,g.storage_method,g.category_id,s.store_name FROM listing AS l INNER JOIN goods_master AS g ON l.goods_id = g.goods_id INNER JOIN store AS s ON l.store_id = s.store_id WHERE l.listing_status = 1 AND l.quantity > 0 AND l.sale_end > GETDATE() AND s.store_status = 0 AND l.store_id = :store_id ORDER BY l.sale_end ASC";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('Listing')){
            $data[] = $row;
        }
        return $data;

    }

    //ユーザー用、おすすめの出品情報を期限が早い順に取得
    public function get_recommend_listing(){
        $dbh = DAO::get_db_connect();

            $sql="SELECT l.*,g.goods_name,g.goods_image,g.unit_weight,g.allergen,g.goods_description,g.storage_method,g.category_id,s.store_name FROM listing AS l INNER JOIN goods_master AS g ON l.goods_id = g.goods_id INNER JOIN store AS s ON l.store_id = s.store_id WHERE l.listing_status = 1 AND l.quantity > 0 AND l.sale_end > GETDATE() AND s.store_status = 0 AND l.recommend = 1 ORDER BY l.sale_end ASC";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('Listing')){
            $data[] = $row;
        }
        return $data;
    }

    //出品IDで、1件の出品情報を取得
    public function get_listing_by_listing_id(int $listing_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT l.*,g.goods_name,g.goods_image,g.unit_weight,g.allergen,g.goods_description,g.storage_method,g.category_id,s.store_name FROM listing AS l INNER JOIN goods_master AS g ON l.goods_id = g.goods_id INNER JOIN store AS s ON l.store_id = s.store_id WHERE l.listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchObject('Listing');
    }

    //店舗用、自分の店舗が出品している出品情報を取得
    public function get_listing_by_store(int $store_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT l.*,g.goods_name,g.goods_image,g.unit_weight,g.allergen,g.goods_description,g.storage_method,g.category_id,s.store_name FROM listing AS l INNER JOIN goods_master AS g ON l.goods_id = g.goods_id INNER JOIN store AS s ON l.store_id = s.store_id WHERE l.store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('Listing')){
            $data[] = $row;
        }
        return $data;
    }

    //店舗用、出品する
    public function insert(Listing $listing){
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO listing(store_id,goods_id,regular_price,selling_price,quantity,food_deadline,recommend,sale_end,created_date) VALUES(:store_id,:goods_id,:regular_price,:selling_price,:quantity,:food_deadline,:recommend,:sale_end,GETDATE())";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$listing->store_id,PDO::PARAM_INT);
        $stmt->bindValue(':goods_id',$listing->goods_id,PDO::PARAM_INT);
        $stmt->bindValue(':regular_price',$listing->regular_price,PDO::PARAM_INT);
        $stmt->bindValue(':selling_price',$listing->selling_price,PDO::PARAM_INT);
        $stmt->bindValue(':quantity',$listing->quantity,PDO::PARAM_INT);
        $stmt->bindValue(':food_deadline',$listing->food_deadline,PDO::PARAM_STR);
        $stmt->bindValue(':recommend',$listing->recommend,PDO::PARAM_INT);
        $stmt->bindValue(':sale_end',$listing->sale_end,PDO::PARAM_STR);

        $stmt->execute();

    }

    //店舗の「出品編集」用、通常価格、販売価格、食品期限、おすすめを変更する
    public function update_listing(int $store_id,int $listing_id,int $regular_price,int $selling_price,string $food_deadline,int $recommend,string $sale_end){
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE listing SET regular_price = :regular_price,selling_price = :selling_price,food_deadline = :food_deadline,recommend = :recommend,sale_end = :sale_end WHERE store_id = :store_id AND listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);
        $stmt->bindValue(':regular_price',$regular_price,PDO::PARAM_INT);
        $stmt->bindValue(':selling_price',$selling_price,PDO::PARAM_INT);
        $stmt->bindValue(':food_deadline',$food_deadline,PDO::PARAM_STR);
        $stmt->bindValue(':recommend',$recommend,PDO::PARAM_INT);
        $stmt->bindValue(':sale_end',$sale_end,PDO::PARAM_STR);

        $stmt->execute();
    }

    //店舗の「在庫調整」用、在庫を変更する
    public function update_quantity(int $store_id,int $listing_id,int $quantity){
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE listing SET quantity = :quantity WHERE store_id = :store_id AND listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);
        $stmt->bindValue(':quantity',$quantity,PDO::PARAM_INT);

        $stmt->execute();
    }

    //この出品に注文が入っているか
    public function has_orders(int $listing_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM order_detail WHERE listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);

        $stmt->execute();

        if($stmt->fetch() !== false){
            return true;
        }
        else{
            return false;
        }
    }

    //店舗用、出品と出品停止を切り替える
    public function update_listing_status(int $store_id,int $listing_id,int $listing_status){
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE listing SET listing_status = :listing_status WHERE store_id = :store_id AND listing_id = :listing_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_id',$listing_id,PDO::PARAM_INT);
        $stmt->bindValue(':listing_status',$listing_status,PDO::PARAM_INT);

        $stmt->execute();

    }




}