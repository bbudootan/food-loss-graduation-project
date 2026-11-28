<?php
$stock = (int)$product['quantity']; // 在庫数
$soldOut = $stock <= 0; // 在庫数が0以下の場合は売り切れに判定
$storeDetailUrl = 'store_detail.php?' . http_build_query(['store_id' => (int) $product['store_id']] + ($isMemberPreview ? ['preview' => 'member'] : [])); // 店舗詳細ページへのリンクを生成
$productSaleEnd = new DateTimeImmutable($product['sale_end']); // 商品の販売終了日時
$foodDeadline = new DateTimeImmutable($product['food_deadline']);
$details = [
    '商品説明' => $product['goods_description'],
    '内容量' => number_format($product['unit_weight']) . 'g',
    'アレルゲン' => $product['allergen'],
    '食品期限' => $foodDeadline->format('n/j H:i'),
    '保存方法' => $product['storage_method'],
]; // カード裏面に表示する商品詳細
?>

<!-- inert: 売り切れの場合はカード全体（リンクやボタン）を操作不可にする -->
<article class="product-flip-card<?= $soldOut ? ' product-soldout' : '' ?>" data-product-card data-stock="<?= $stock ?>" <?= $soldOut ? ' inert' : '' ?>>
    <div class="product-flip-inner">
        <!-- 1. 商品カードの表面 -->
        <div class="card product-flip-front" data-card-front>
            <!-- 1-1. 商品画像と在庫表示 -->
            <div class="card-img-box border-bottom d-flex align-items-center justify-content-center">
                <span class="corner-ribbon"><?= $soldOut ? '売り切れ' : '残り：' . $stock . '点' ?></span>
                <?php if (!empty($product['goods_image'])): ?>
                    <img src="../images/<?= rawurlencode($product['goods_image']) ?>" class="card-photo <?= $soldOut ? 'opacity-50' : '' ?>">
                <?php else: ?>
                    <span class="text-secondary small <?= $soldOut ? 'opacity-50' : '' ?>"><span class="bi bi-image me-1"></span>商品写真</span>
                <?php endif; ?>
            </div>

            <!-- 1-2. 商品情報と購入操作 -->
            <div class="card-body p-3 d-flex flex-column justify-content-between <?= $soldOut ? 'opacity-50' : '' ?>">
                <div>
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($product['goods_name']) ?></h6>
                    <div class="small mb-1">
                        <a class="link-secondary text-decoration-none" href="<?= htmlspecialchars($storeDetailUrl) ?>"><?= htmlspecialchars($product['store_name']) ?></a>
                    </div>
                    <div class="mb-1">
                        <span class="small text-secondary">通常価格 <del>¥<?= number_format($product['regular_price']) ?></del></span>
                        <span class="mx-1">&gt;</span>
                        <span class="fw-bold fs-5">¥<?= number_format($product['selling_price']) ?></span>
                    </div>
                    <div class="small text-secondary mb-2"><?= $productSaleEnd->format('H:i') ?>まで受取可</div>
                </div>

                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">購入数量</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <!-- 数量を減らす -->
                            <button type="button" class="btn btn-outline-secondary" data-quantity-step="-1" disabled>−</button>
                            <output class="btn btn-outline-secondary disabled text-dark" data-quantity><?= $soldOut ? 0 : 1 ?></output>
                            <!-- 数量を増やす -->
                            <button type="button" class="btn btn-outline-secondary" data-quantity-step="1" <?= $stock <= 1 ? 'disabled' : '' ?>>＋</button>
                        </div>
                    </div>
                    <div class="d-grid mb-2">
                        <!-- ★カートへの追加機能まだ実装できない -->
                        <button type="button" class="btn btn-outline-dark btn-sm">
                            <span class="bi bi-cart3 me-1"></span>カートに入れる
                        </button>
                    </div>

                    <div class="text-end">
                        <button type="button" class="flip-toggle-link" data-flip-card="back">商品について &rarr;</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. 商品カードの裏面（売り切れでない場合のみ表示） -->
        <?php if (!$soldOut): ?>
            <div class="card product-flip-back" data-card-back inert>
                <div class="card-body p-3 d-flex flex-column">
                    <h6 class="fw-bold border-bottom pb-2 mb-3"><?= htmlspecialchars($product['goods_name']) ?></h6>
                    <dl class="small flex-grow-1 card-details">
                        <?php foreach ($details as $label => $value): ?>
                            <?php if ($value !== null && $value !== ''): ?>
                                <dt><?= htmlspecialchars($label) ?></dt>
                                <dd><?= htmlspecialchars($value) ?></dd>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </dl>
                    <div class="text-end border-top pt-2">
                        <button type="button" class="flip-toggle-link" data-flip-card="front">商品に戻る &rarr;</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>