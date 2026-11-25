<?php
require_once 'DAO.php';

class Account{
    public $account_id;
    public $account_type;
    public $email;
    public $password;
    public $account_name;
    public $account_status;
    public $status_reason;
    public $created_date;
    public $avatar_image;
}

//ログイン用、メールアドレスとパスワードがあっていれば、Accountを返す。違ったらfalse
class AccountDAO{
    public function get_account(string $email,string $password){
        $dbh = DAO::get_db_connect();

        $sql="SELECT * FROM account WHERE email = :email";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();

        $account=$stmt->fetchObject('Account');

        if($account !== false){

            if(password_verify($password,$account->password)){
                return $account;
            }
        }

        return false;
    }
    //ユーザー用、アカウントIDで1件取り出す
    public function get_account_by_id(int $account_id){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM account WHERE account_id = :account_id";

        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject('Account');
    }

    //会員登録の前に、emailが登録されているかのチェック。存在していたらtrue、存在してなかったらfalseを返す
    public function email_exists(string $email){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM account WHERE email = :email";

        $stmt =$dbh->prepare($sql);
        $stmt->bindValue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();

        //emailが存在していたら
        if($stmt->fetch() !== false){
            return true;
        }
        else{
            return false;
        }
    }

    //新規会員登録用
    public function insert(Account $account){
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO account(account_type,email,password,account_name,created_date,avatar_image) VALUES(:account_type,:email,:password,:account_name,GETDATE(),:avatar_image)";

        $stmt = $dbh->prepare($sql);
        $password = password_hash($account->password,PASSWORD_DEFAULT);

        $stmt->bindValue(':account_type',$account->account_type,PDO::PARAM_STR);
        $stmt->bindValue(':email',$account->email,PDO::PARAM_STR);
        $stmt->bindValue(':password',$password,PDO::PARAM_STR);
        $stmt->bindValue(':account_name',$account->account_name,PDO::PARAM_STR);
        $stmt->bindValue(':avatar_image',$account->avatar_image,PDO::PARAM_STR);

        $stmt->execute();
        
    }

    public function update_avatar_image(int $account_id,string $avatar_image){
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE account SET avatar_image = :avatar_image WHERE account_id = :account_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':account_id',$account_id,PDO::PARAM_INT);
        $stmt->bindValue(':avatar_image',$avatar_image,PDO::PARAM_STR);
        
        $stmt->execute();
    }
}