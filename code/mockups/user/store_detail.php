<?php
// 1. views/layout.php で使用する会員情報やカート情報などの準備
require __DIR__ . '/includes/common_layout_data.php';

// 2. views/store_detail_view.php に渡す店舗詳細データの準備
// 2-1. 店舗詳細用データの取得
$store_id = filter_input(INPUT_GET, 'store_id', FILTER_VALIDATE_INT);
$store = $store_id !== false && $store_id !== null ? ($storesById[$store_id] ?? null) : null;

// 2-2. この店舗の在庫がある商品を絞り込む
$store_product_list = $store === null ? [] : array_values(array_filter($catalog['products'], static fn(array $product): bool => (int) $product['store_id'] === $store_id && (int) $product['quantity'] > 0));

// 2-3. 店舗が見つからない場合は404を返す
if ($store === null) http_response_code(404);

// 3. layout.php に渡すページ情報の準備
$storeListingUrl = $isMemberPreview ? 'store_listing.php?preview=member' : 'store_listing.php'; // 店舗一覧へのリンク。モックの会員プレビュー状態も引き継ぐ
$pageTitle = $store !== null ? $store['store_name'] . '・店舗詳細' : '店舗が見つかりません';
$breadcrumbs = [
    ['label' => 'ホーム', 'url' => $homeUrl],
    ['label' => '店舗一覧', 'url' => $storeListingUrl],
    ['label' => $store['store_name'] ?? '店舗が見つかりません'],
];
$pageView = __DIR__ . '/views/store_detail_view.php'; // ★今回表示するメインコンテンツに /views/store_detail_view.php を指定

require __DIR__ . '/views/layout.php';

// ★注：画面設定用の変数はキャメルケース、業務データ用の変数はスネークケースで命名