<?php
// 1. views/layout.php で使用する会員情報やカート情報などの準備
require __DIR__ . '/includes/common_layout_data.php';

// 2. views/homepage_view.php に渡すデータの準備
// 2-1. 「本日のおすすめ」に配置する商品の抽出
$recommended_product_list = array_values(array_filter($catalog['products'], static fn(array $product): bool => (int) $product['recommend'] === 1));

// 2-2. 「近所の人気店」に表示する店舗一覧を取得
$store_list = $catalog['stores'];
// store_detail.php と同じ条件で、各店舗の出品中商品数を数える
foreach ($store_list as $index => $store) {
    $store_product_list = array_filter($catalog['products'], static fn(array $product): bool => (int) $product['store_id'] === (int) $store['store_id'] && (int) $product['quantity'] > 0);
    $store_list[$index]['listing_count'] = count($store_product_list);
}

// 3. layout.php に渡すページ情報の準備
$pageTitle = 'ホーム';
$breadcrumbs = [['label' => 'ホーム']];
$pageView = __DIR__ . '/views/homepage_view.php'; // ★今回表示するメインコンテンツに /views/homepage_view.php を指定

require __DIR__ . '/views/layout.php';

// ★注：画面設定用の変数はキャメルケース、業務データ用の変数はスネークケースで命名