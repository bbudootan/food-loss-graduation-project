<?php
require_once 'DAO.php';

class GoodsMaster{
    public $goods_id;
    public $goods_name;
    public $barcode;
    public $category_id;
    public $unit_weight;
    public $goods_description;
    public $allergen;
    public $storage_method;
    public $goods_status;
    public $goods_changed;
    public $goods_image;
    public $created_date;
    public $updated_date;
    //カテゴリーテーブルから持ってきて表示する用
    public $category_name;
}

class GoodsMasterDAO{
    //管理者用、商品マスターの全データを取得
    public function get_goods_master(){
        $dbh = DAO::get_db_connect();

        $sql="SELECT g.*,c.category_name FROM goods_master AS g INNER JOIN category AS c ON g.category_id = c.category_id";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('GoodsMaster')){
            $data[] = $row;
        }
        return $data;
    }

    //店舗用、商品フラグが有効の商品マスターのデータを取得
    public function get_active_goods_master(){
        $dbh = DAO::get_db_connect();

        $sql="SELECT g.*,c.category_name FROM goods_master AS g INNER JOIN category AS c ON g.category_id = c.category_id WHERE g.goods_status = 1";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('GoodsMaster')){
            $data[] = $row;
        }
        return $data;
    }

    //管理者用、商品名で検索し、商品名が含まれていた商品マスターをすべて取得
    public function get_goods_master_by_goods_name(string $goods_name){
        $dbh = DAO::get_db_connect();

        $sql="SELECT g.*,c.category_name FROM goods_master AS g INNER JOIN category AS c ON g.category_id = c.category_id WHERE g.goods_name LIKE :goods_name ORDER BY goods_id DESC";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':goods_name','%'.$goods_name.'%',PDO::PARAM_STR);
        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('GoodsMaster')){
            $data[] = $row;
        }
        return $data;
    }

    //商品名で検索し、商品名が含まれていて、かつ、有効な商品マスターを取得
    public function get_active_goods_master_by_goods_name(string $goods_name){
        $dbh = DAO::get_db_connect();

        $sql="SELECT g.*,c.category_name FROM goods_master AS g INNER JOIN category AS c ON g.category_id = c.category_id WHERE g.goods_name LIKE :goods_name AND g.goods_status = 1 ORDER BY goods_id DESC";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':goods_name','%'.$goods_name.'%',PDO::PARAM_STR);
        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('GoodsMaster')){
            $data[] = $row;
        }
        return $data;
    }

    //同じバーコードの商品がないか調べる
    public function barcode_exists(string $barcode,int $goods_id = 0){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM goods_master WHERE barcode = :barcode AND goods_id != :goods_id";

        $stmt =$dbh->prepare($sql);
        $stmt->bindValue(':barcode',$barcode,PDO::PARAM_STR);
        $stmt->bindValue(':goods_id',$goods_id,PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->fetch() !== false){
            return true;
        }
        else{
            return false;
        }
    }

    //商品IDで1件の商品マスターを取得、とれなかったらfalse
    public function get_goods_master_by_goods_id(int $goods_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT g.*,c.category_name FROM goods_master AS g INNER JOIN category AS c ON g.category_id = c.category_id WHERE g.goods_id = :goods_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':goods_id',$goods_id,PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject('GoodsMaster');

    }

    //管理者用、商品マスターを編集する
    public function update_goods_master(GoodsMaster $goods_master){
        $dbh = DAO::get_db_connect();

        $sql="UPDATE goods_master SET goods_name = :goods_name,barcode = :barcode,category_id = :category_id,unit_weight = :unit_weight,goods_description = :goods_description,allergen = :allergen,storage_method = :storage_method,goods_image = :goods_image,goods_changed = 1,updated_date = GETDATE() WHERE goods_id = :goods_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':goods_id',$goods_master->goods_id,PDO::PARAM_INT);
        $stmt->bindValue(':goods_name',$goods_master->goods_name,PDO::PARAM_STR);
        $stmt->bindValue(':barcode',$goods_master->barcode,PDO::PARAM_STR);
        $stmt->bindValue(':category_id',$goods_master->category_id,PDO::PARAM_INT);
        $stmt->bindValue(':unit_weight',$goods_master->unit_weight,PDO::PARAM_INT);
        $stmt->bindValue(':goods_description',$goods_master->goods_description,PDO::PARAM_STR);
        $stmt->bindValue(':allergen',$goods_master->allergen,PDO::PARAM_STR);
        $stmt->bindValue(':storage_method',$goods_master->storage_method,PDO::PARAM_STR);
        $stmt->bindValue(':goods_image',$goods_master->goods_image,PDO::PARAM_STR);

        $stmt->execute();
    }

    //商品の有効、無効を切り替える
    public function update_goods_status(int $goods_id,int $goods_status){
        $dbh = DAO::get_db_connect();

        $sql="UPDATE goods_master SET goods_status = :goods_status WHERE goods_id = :goods_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':goods_id',$goods_id,PDO::PARAM_INT);
        $stmt->bindValue(':goods_status',$goods_status,PDO::PARAM_INT);

        $stmt->execute();
    }

    //商品を登録
    public function insert(GoodsMaster $goods_master){
         $dbh = DAO::get_db_connect();

         $sql = "INSERT INTO goods_master(goods_name,barcode,category_id,unit_weight,goods_description,allergen,storage_method,goods_image,created_date) VALUES(:goods_name,:barcode,:category_id,:unit_weight,:goods_description,:allergen,:storage_method,:goods_image,GETDATE())";

         $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':goods_name',$goods_master->goods_name,PDO::PARAM_STR);
        $stmt->bindValue(':barcode',$goods_master->barcode,PDO::PARAM_STR);
        $stmt->bindValue(':category_id',$goods_master->category_id,PDO::PARAM_INT);
        $stmt->bindValue(':unit_weight',$goods_master->unit_weight,PDO::PARAM_INT);
        $stmt->bindValue(':goods_description',$goods_master->goods_description,PDO::PARAM_STR);
        $stmt->bindValue(':allergen',$goods_master->allergen,PDO::PARAM_STR);
        $stmt->bindValue(':storage_method',$goods_master->storage_method,PDO::PARAM_STR);
        $stmt->bindValue(':goods_image',$goods_master->goods_image,PDO::PARAM_STR);

        $stmt->execute();
    }
}