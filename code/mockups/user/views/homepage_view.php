<?php require __DIR__ . '/components/campaign_carousel.php'; ?>

<section class="mb-5" id="products">
    <h5 class="fw-bold mb-3">本日のおすすめ</h5>
    <?php
    $carousel_product_list = $recommended_product_list;
    $carouselId = 'productCarousel';
    $carousel_empty_message = '現在、おすすめの出品はありません。';
    require __DIR__ . '/components/product_carousel.php';
    ?>
</section>

<section class="mb-4" id="shops">
    <h5 class="fw-bold mb-3">近所の人気店</h5>

    <?php if ($store_list === []): ?>
        <p class="text-secondary">現在、表示できる店舗はありません。</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($store_list as $index => $store): ?>
                <!-- 4件目以降は最初は非表示 -->
                <div class="col-12 col-md-6 col-lg-4 store-item <?= $index >= 3 ? 'd-none' : '' ?>">
                    <?php require __DIR__ . '/components/store_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($store_list) > 3): ?>
            <div class="text-center mt-3">
                <button type="button" id="showMoreStores" class="btn btn-outline-brown btn-sm px-4">もっと見る &#9660;</button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>