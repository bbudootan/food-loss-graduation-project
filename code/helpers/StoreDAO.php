<?php
require_once 'DAO.php';

class Store{
    public $store_id;
    public $account_id;
    public $store_name;
    public $address;
    public $tel;
    public $opening_time;
    public $closing_time;
    public $store_description;
    public $store_genre;
    public $store_image;
    public $store_status;
    public $created_date;
    public $listing_count;  //追加、ユーザー側の店舗一覧の店舗カードの出品中の件数
}

class StoreDAO{

    //ログインした店舗関係者のアカウントIDから、その店舗を取得する
    public function get_store_by_account_id(int $account_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT * FROM store WHERE account_id = :account_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject('Store');
    }

    //店舗IDで、1件取り出す(店舗詳細用)
    public function get_store_by_store_id(int $store_id){
        $dbh = DAO::get_db_connect();

        $sql="SELECT * FROM store WHERE store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject('Store');
    }

    //ユーザーに見せる用、利用可能な店舗を取得
    public function get_open_store(){
        $dbh = DAO::get_db_connect();

        $sql="SELECT * FROM store WHERE store_status = 0 ORDER BY store_id DESC";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('Store')){
            $data[] = $row;
        }
        return $data;
    }

    //追加
    //店舗一覧・ホーム用、店舗名・住所とジャンルで利用中の店舗を探す（出品中の件数つき）
    //キーワードが '' なら全部、ジャンルが '' なら絞り込まない
    public function get_open_store_by_keyword_and_genre(string $keyword,string $genre){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT s.*,(SELECT COUNT(*) FROM listing AS l WHERE l.store_id = s.store_id AND l.listing_status = 1 AND l.quantity > 0 AND l.sale_end > GETDATE()) AS listing_count FROM store AS s WHERE s.store_status = 0 AND (s.store_name LIKE :keyword1 OR s.address LIKE :keyword2)";

        if($genre !== ''){
            $sql .= " AND s.store_genre = :store_genre";
        }

        $sql .= " ORDER BY listing_count DESC,s.store_id ASC";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':keyword1','%'.$keyword.'%',PDO::PARAM_STR);
        $stmt->bindValue(':keyword2','%'.$keyword.'%',PDO::PARAM_STR);
        if($genre !== ''){
            $stmt->bindValue(':store_genre',$genre,PDO::PARAM_STR);
        }

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetchObject('Store')){
            $data[] = $row;
        }
        return $data;
    }

    //管理者用、停止、退店も含め全店舗を取得する
    public function get_all_store(){
        $dbh = DAO::get_db_connect();

        $sql="SELECT * FROM store ORDER BY store_id DESC";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('Store')){
            $data[] = $row;
        }
        return $data;        
    }

    //管理者用、受け取った店舗IDの店舗状態を変更する
    public function update_store_status(int $store_id, int $store_status){
        $dbh = DAO::get_db_connect();

        $sql="UPDATE store SET store_status = :store_status WHERE store_id = :store_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':store_id',$store_id,PDO::PARAM_INT);
        $stmt->bindValue(':store_status',$store_status,PDO::PARAM_INT);
        $stmt->execute();

    }
}

