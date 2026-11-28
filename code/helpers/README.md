# ユーザー側で使うDAOとメソッド（helpers/）

一般ユーザーの画面から使うDAOのメソッドと、その使い方のまとめです。
メソッド名・引数・戻り値は、helpers/ にある今のDAO（11/28時点）に合わせています。

## 1. 共通のきまり

### 読み込み方

```php
require_once '../helpers/ListingDAO.php';   // 画面の場所に合わせてパスを変える
$listingDAO = new ListingDAO();
```

### セッション

ログインしたら、次の値がセッションに入ります（共通の login.php で入れる）。

| キー | 中身 |
| --- | --- |
| `$_SESSION['account_id']` | アカウントID |
| `$_SESSION['account_type']` | 'user'・'store'・'admin' のどれか |

ユーザーだけが使う画面（カート・注文など）は、最初に `$_SESSION['account_type']` が 'user' か確かめます。

### 戻り値の見方

| 戻り値 | 意味 |
| --- | --- |
| オブジェクト（例：Account） | 1件見つかった。`$account->account_name` のように使う |
| false | 見つからなかった（1件取り出すメソッド） |
| 配列 | 何件も取り出すメソッド。0件なら空の配列 `[]` |
| 数字 | 件数・合計など |
| なし | 登録・更新だけするメソッド |

### 画面側でやること

- 画面に出す文字は、必ず `htmlspecialchars()` を通す
- 空欄を NULL にしたいときは、DAO に渡す前に画面の PHP で `if ($x === '') { $x = null; }` とする
- SQL Server から返る数字は文字列のことがあるので、比べるときは `(int)` を付ける（例：`(int)$listing->listing_status === 1`）
- 日時はPHPのタイムゾーンで計算するところがあるので、php.ini の `date.timezone` を `Asia/Tokyo` にしておく（なっていないと受け取り締め切りが数時間ずれる）

## 2. DAOごとのメソッド

### AccountDAO（account）

| メソッド | 戻り値 | 使う画面 | メモ |
| --- | --- | --- | --- |
| `get_account(string $email, string $password)` | Account / false | ログイン | メールで探して password_verify で照合。**利用状態（account_status）は見ていない**ので、画面で 0（利用可）か確かめる |
| `get_account_by_id(int $account_id)` | Account / false | マイページ、ヘッダーの会員アイコン | ログイン中の人の情報を出す |
| `email_exists(string $email)` | true / false | 会員登録 | 登録済みのメールなら true |
| `insert(Account $account)` | なし | 会員登録 | パスワードは DAO の中でハッシュ化する。account_type は 'user' を入れて渡す。avatar_image は null でよい |
| `update_avatar_image(int $account_id, string $avatar_image)` | なし | アバターの変更 | 画像ファイルは画面で images/avatar_image に保存してから、ファイル名を渡す |

### StoreDAO（store）

| メソッド | 戻り値 | 使う画面 | メモ |
| --- | --- | --- | --- |
| `get_open_store_by_keyword_and_genre(string $keyword, string $genre)` | Store の配列 | 店舗一覧、ホームの「近所の人気店」 | 利用中の店舗だけ。キーワードは店舗名・住所の一部、'' なら全部。ジャンルは 'ベーカリー'・'お弁当・惣菜'・'スイーツ・洋菓子'、'' なら絞り込まない。出品中の件数（`listing_count`）つきで、件数の多い順 |
| `get_store_by_store_id(int $store_id)` | Store / false | 店舗詳細、注文確認（店舗名・住所） | 停止中の店舗も取り出すので、店舗詳細では store_status が 0 か確かめる |
| `get_open_store()` | Store の配列 | （予備） | 利用中の店舗を全部。件数はつかない |

### CategoryDAO（category）

| メソッド | 戻り値 | 使う画面 | メモ |
| --- | --- | --- | --- |
| `get_valid_category()` | Category の配列 | 商品一覧のカテゴリの選択肢 | option の value は category_id、表示は category_name |

### ListingDAO（listing）

ユーザー用のメソッドは、どれも「出品中・在庫あり・締め切り前・店舗が利用可」の出品だけを返します（get_listing_by_listing_id だけは例外）。
返る Listing には、出品の列に加えて goods_name・goods_image・unit_weight・allergen・goods_description・storage_method・category_id・store_name が入ります。

| メソッド | 戻り値 | 使う画面 | メモ |
| --- | --- | --- | --- |
| `get_listing_for_user_by_search(string $keyword, int $category_id, string $sort)` | Listing の配列 | 商品一覧 | キーワードは商品名・店舗名の一部（'' なら全部）。カテゴリ 0 なら全部。並び順は 'price-asc'（価格が安い順）・'discount-desc'（割引率が高い順）・それ以外（おすすめ順） |
| `get_recommend_listing()` | Listing の配列 | ホームの「本日のおすすめ」 | おすすめ（recommend = 1）だけ。締め切りが近い順 |
| `get_listing_for_user_by_store(int $store_id)` | Listing の配列 | 店舗詳細 | その店舗の今買える出品 |
| `get_listing_by_listing_id(int $listing_id)` | Listing / false | 商品詳細、カートに入れる前の確認 | **出品停止・在庫0・締め切り後も取り出す**ので、画面で確かめる（下の使い方を見る） |
| `get_listing_for_user()` | Listing の配列 | （予備） | 今買える出品を全部。`get_listing_for_user_by_search('', 0, '')` とほぼ同じ |

### CartDAO（cart）

1店舗につき合計10点まで（`CartDAO::MAX_PER_STORE`）。

| メソッド | 戻り値 | 使う画面 | メモ |
| --- | --- | --- | --- |
| `get_cart(int $account_id)` | Cart の配列 | カート | 店舗ID順に並ぶ。`quantity` はカートに入れた数、`stock` は残りの在庫。store_name・goods_name・goods_image・selling_price・listing_status・sale_end も入る |
| `get_store_total(int $account_id, int $store_id)` | 数字 | カートに入れる前 | その店舗の商品がカートに何点あるか。なければ 0 |
| `add(int $account_id, int $listing_id, int $quantity)` | なし | 商品詳細の「カートに入れる」 | 同じ商品がもうあれば数を足す。**10点の確認はしないので、先に get_store_total で確かめる** |
| `update_cart_quantity(int $account_id, int $listing_id, int $quantity)` | なし | カートの ＋・－ | 数を上書きする。10点を超えないか、在庫（stock）を超えないかは画面で確かめる |
| `delete(int $account_id, int $listing_id)` | なし | カートの削除 | 1行消す |
| `read_cart(int $account_id)` | 配列 | カートのドロワー | Yさん担当。店舗ごとにまとめた配列を返す |

### OrdersDAO（orders・order_detail）

注文の状態（order_status）：0=受け取り待ち、1=受け取り済み、3=キャンセル（店舗都合）。

| メソッド | 戻り値 | 使う画面 | メモ |
| --- | --- | --- | --- |
| `get_pickup_deadline(int $account_id, int $store_id)` | 日時の文字 / false | 注文確認 | 注文する前に受け取り締め切りを計算する。カートにその店舗の商品がなければ false |
| `create_order(int $account_id, int $store_id)` | 注文ID / false | 注文確認の「注文を確定」 | カートのうち1店舗分を注文にする。在庫を減らし、カートから消すところまで全部まとめて行う。在庫不足・締め切り後などなら false（何も変わらない） |
| `get_order_for_account(int $order_id, int $account_id)` | Orders / false | 注文完了、注文詳細 | 自分の注文だけ取り出せる（ほかの人の注文番号なら false）。store_name つき |
| `get_orders_by_account(int $account_id)` | Orders の配列 | 注文履歴 | 新しい注文から順。store_name つき |
| `get_order_details(int $order_id)` | OrderDetail の配列 | 注文詳細、注文完了 | goods_name・goods_image・selling_price・quantity。注文のあとに商品マスターが変わっていたら `goods_changed` が 1 |

受け取り締め切り（pickup_deadline）は、「今日の閉店時刻（過ぎていれば翌日の閉店時刻）」と「注文する商品の sale_end のうちいちばん早いもの」の早いほうです（D-05）。注文確認で出す時刻と、注文後に保存される時刻は同じ計算なのでずれません。

## 3. 画面ごとの使い方（例）

### ログイン

```php
$accountDAO = new AccountDAO();
$account = $accountDAO->get_account($_POST['email'], $_POST['password']);

if ($account === false) {
    $error = 'メールアドレスかパスワードが違います。';
} else if ((int)$account->account_status !== 0) {
    $error = 'このアカウントは利用できません。';
} else {
    $_SESSION['account_id'] = (int)$account->account_id;
    $_SESSION['account_type'] = $account->account_type;
    // account_type で行き先を分ける（店舗なら store_id もセッションに入れる）
}
```

### 会員登録

```php
$accountDAO = new AccountDAO();

if ($accountDAO->email_exists($email)) {
    $error = 'このメールアドレスは登録されています。';
} else {
    $account = new Account();
    $account->account_type = 'user';
    $account->email = $email;
    $account->password = $password;        // ハッシュ化は DAO の中でする
    $account->account_name = $account_name;
    $account->avatar_image = null;
    $accountDAO->insert($account);
}
```

### 商品一覧

```php
$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}
$category_id = 0;
if (isset($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];
}
$sort = '';
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
}

$listings = $listingDAO->get_listing_for_user_by_search($keyword, $category_id, $sort);
$categories = $categoryDAO->get_valid_category();

foreach ($listings as $listing) {
    // 割引率（%）は画面で計算する
    $discount = (int)(((int)$listing->regular_price - (int)$listing->selling_price) * 100 / (int)$listing->regular_price);
    // 受け取り・販売の締め切りは「○時まで」で出す
    $limit = date('H:i', strtotime($listing->sale_end)) . 'まで';
}
```

### 商品詳細とカートに入れる

```php
$listing = $listingDAO->get_listing_by_listing_id((int)$_GET['listing_id']);

// 今買える出品か確かめる（get_listing_by_listing_id は条件なしで取り出すため）
$can_buy = false;
if ($listing !== false) {
    if ((int)$listing->listing_status === 1 && (int)$listing->quantity > 0 && strtotime($listing->sale_end) > time()) {
        $can_buy = true;
    }
}

// 「カートに入れる」が押されたとき
if ($can_buy && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = (int)$_POST['quantity'];
    $total = $cartDAO->get_store_total($_SESSION['account_id'], (int)$listing->store_id);

    if ($total + $quantity > CartDAO::MAX_PER_STORE) {
        $error = '1店舗につき合計10点までです。';
    } else if ($quantity > (int)$listing->quantity) {
        $error = '在庫が足りません。';
    } else {
        $cartDAO->add($_SESSION['account_id'], (int)$listing->listing_id, $quantity);
    }
}
```

### カート（店舗ごとにまとめて出す）

get_cart は店舗ID順に並んでいるので、店舗IDが変わったところで見出しを出します。

```php
$carts = $cartDAO->get_cart($_SESSION['account_id']);
$before_store_id = 0;

foreach ($carts as $cart) {
    if ((int)$cart->store_id !== $before_store_id) {
        // ここで店舗名（$cart->store_name）の見出しと「この店舗の注文手続きへ」のリンクを出す
        // リンク先：order_confirm.php?store_id=店舗ID
        $before_store_id = (int)$cart->store_id;
    }
    // 商品の行：goods_name、selling_price × quantity、sale_end（○時まで）
}
```

### 注文確認と注文の確定

```php
$store_id = (int)$_GET['store_id'];     // POST のときは $_POST['store_id']
$store = $storeDAO->get_store_by_store_id($store_id);
$deadline = $ordersDAO->get_pickup_deadline($_SESSION['account_id'], $store_id);

if ($store === false || $deadline === false) {
    // カートにこの店舗の商品がない → カートへ戻す
}

// 画面には「受取期限：○月○日 19:00 まで」と出す
$deadline_text = date('n月j日 H:i', strtotime($deadline)) . ' まで';

// 「注文を確定」が押されたとき
$order_id = $ordersDAO->create_order($_SESSION['account_id'], $store_id);
if ($order_id === false) {
    $error = '在庫が足りないか、締め切りを過ぎた商品があります。カートを確認してください。';
} else {
    header('Location: order_complete.php?order_id=' . $order_id);
    exit;
}
```

受取予定時間（時間帯）は選ばせません。受取期限だけを出します（D-05）。

### 注文完了・注文詳細（受け取り確認コード）

```php
$order = $ordersDAO->get_order_for_account((int)$_GET['order_id'], $_SESSION['account_id']);
if ($order === false) {
    // ほかの人の注文・ない番号 → 注文履歴へ戻す
}
$details = $ordersDAO->get_order_details((int)$order->order_id);

// 受け取り確認コードは「店舗ID-注文ID」（例：1-100）。DBには保存しないので画面で作る（D-25）
$pickup_code = $order->store_id . '-' . $order->order_id;
```

### 注文履歴

```php
$orders = $ordersDAO->get_orders_by_account($_SESSION['account_id']);

foreach ($orders as $order) {
    $status = (int)$order->order_status;
    if ($status === 0) {
        // 受け取り待ち：受け取り確認コードと受取期限を出す
    } else if ($status === 1) {
        // 受け取り済み
    } else if ($status === 3) {
        // 「店舗都合によりキャンセルされました」と出す（D-28）
    }
}
```

注文詳細で `$detail->goods_changed` が 1 なら、「商品情報が変更されています」と出します。

## 4. まだないもの

- 会員情報の変更・退会・パスワード変更などのメソッドは、まだありません。MVPで必要なら作ろうと思います。
