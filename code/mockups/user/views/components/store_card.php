<?php
// ★★モックアップで店舗詳細ページへのリンクを生成する用
// 実装後previewパラメータを削除し、認証済みセッションを使用予定★★
$storeDetailUrl = 'store_detail.php?' . http_build_query(['store_id' => (int) $store['store_id']] + ($isMemberPreview ? ['preview' => 'member'] : []));
//
?>

<article class="card h-100 rounded-3 overflow-hidden">
    <div class="shop-img-box border-bottom d-flex align-items-center justify-content-center">
        <?php if (!empty($store['store_image'])): ?>
            <img src="../images/<?= rawurlencode($store['store_image']) ?>" class="card-photo">
        <?php else: ?>
            <span class="text-secondary small"><span class="bi bi-shop me-1"></span>店舗写真</span>
        <?php endif; ?>

        <!-- ★★お気に入り実装時：会員の登録状態を取得し、追加・解除処理を接続する★★ -->
        <button type="button" class="btn position-absolute top-0 end-0 z-2 m-2 px-2 text-danger fs-5" aria-label="<?= htmlspecialchars($store['store_name']) ?>をお気に入りに追加" hidden><span class="bi bi-heart" aria-hidden="true"></span></button>

        <span class="position-absolute bottom-0 start-0 m-2 px-2 py-1 bg-white bg-opacity-75 rounded small fw-bold">
            出品中：<?= (int) $store['listing_count'] ?>件
        </span>
    </div>
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-baseline gap-2 mb-1">
            <h6 class="fw-bold mb-0"><?= htmlspecialchars($store['store_name']) ?></h6>
            <?php if ($store['store_genre'] !== null && $store['store_genre'] !== ''): ?>
                <span class="small text-secondary"><?= htmlspecialchars($store['store_genre']) ?></span>
            <?php endif; ?>
        </div>
        <p class="small text-secondary mb-1"><?= htmlspecialchars($store['address']) ?></p>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="small text-secondary mb-0"><span class="bi bi-clock me-1"></span><?= htmlspecialchars(substr($store['opening_time'], 0, 5) . '〜' . substr($store['closing_time'], 0, 5)) ?></p>
            <!-- stretched-link：カード全体を店舗詳細へのリンクにする -->
            <a class="small link-secondary stretched-link" href="<?= htmlspecialchars($storeDetailUrl) ?>">店舗詳細 →</a>
        </div>
    </div>
</article>