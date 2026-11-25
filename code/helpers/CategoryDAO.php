<?php
require_once 'DAO.php';

class Category{
    public $category_id;
    public $category_name;
    public $category_status;

}

class CategoryDAO{

    //有効なカテゴリの取得
    public function get_valid_category(){
        $dbh = DAO::get_db_connect();

        $sql="SELECT * FROM category WHERE category_status = 1 ORDER BY category_id ASC";

        $stmt = $dbh->query($sql);

        $data = [];

        while($row = $stmt->fetchObject('Category')){
            $data[] = $row;
        }
        return $data;        
    }
    
}