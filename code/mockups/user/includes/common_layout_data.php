<?php
// ★★モックアップ用：共通レイアウトで使う会員・カート情報・店舗情報などを準備
// 本番ではセッション情報やDAOの取得結果へ順次置き換える
$isMemberPreview = ($_GET['preview'] ?? null) === 'member';
$member = $isMemberPreview ? ['name' => 'ユーザー名', 'avatar_image' => 'demo_user_avatar.png'] : null;
$catalog = require __DIR__ . '/../mock_data/mock_data.php';
$storesById = array_column($catalog['stores'], null, 'store_id');
$homeUrl = $isMemberPreview ? 'index.php?preview=member' : 'index.php';

$cartStores = [];
$cartQuantity = 0;
$cartTotal = 0;

if ($member !== null) {
    $productsByListingId = array_column($catalog['products'], null, 'listing_id');
    foreach ($catalog['cart'] as $cartItem) {
        $product = $productsByListingId[$cartItem['listing_id']] ?? null;
        if ($product === null) continue;

        $store_id = (int) $product['store_id'];
        $quantity = (int) $cartItem['quantity'];
        $subtotal = (int) $product['selling_price'] * $quantity;
        $store = $storesById[$store_id] ?? null;
        if ($store === null) continue;

        // 店舗ごとの表示情報を初期化し、点数と金額を集計する
        $cartStores[$store_id] ??= [
            'name' => $product['store_name'],
            'closing_time' => $store['closing_time'],
            'pickup_deadline' => null,
            'items' => [],
            'quantity' => 0,
            'subtotal' => 0,
        ];
        $cartStores[$store_id]['items'][] = $product + ['cart_quantity' => $quantity, 'subtotal' => $subtotal];
        $cartStores[$store_id]['quantity'] += $quantity;
        $cartStores[$store_id]['subtotal'] += $subtotal;
        $cartQuantity += $quantity;
        $cartTotal += $subtotal;

        $cartSaleEnd = new DateTimeImmutable($product['sale_end']);
        $closingDeadline = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $cartSaleEnd->format('Y-m-d') . ' ' . $store['closing_time']
        );
        if ($closingDeadline === false) continue;

        // 商品の販売終了日時と店舗の閉店時刻の早い方を受取期限とする
        $pickupDeadline = $cartSaleEnd < $closingDeadline ? $cartSaleEnd : $closingDeadline;
        // 同じ店舗の商品で最も早い受取期限を記録する
        $storePickupDeadline = $cartStores[$store_id]['pickup_deadline'];
        if ($storePickupDeadline === null || $pickupDeadline < $storePickupDeadline) {
            $cartStores[$store_id]['pickup_deadline'] = $pickupDeadline;
        }
    }
}
