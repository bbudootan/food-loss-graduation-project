<?php if ($store !== null): ?>
    <!-- 1. 店舗基本情報カード -->
    <div class="col-12 col-md-10 col-lg-10 mx-auto"> <!-- 中、大画面幅10/12+画像と情報を5:7で横並び・スマホ画面幅12/12+画像と情報を縦並び・mx-autoで中央配置 -->
        <section class="card rounded-3 overflow-hidden mb-4">
            <div class="row g-0">
                <!-- 1-1. 左側・店舗写真枠 -->
                <div class="col-12 col-md-5 d-flex align-items-center justify-content-center border-bottom border-end" style="min-height: 200px;"> <!-- min-height: 画像未設定時も画像枠を維持するため、最小高さを指定。CSSクラスに移す予定 -->
                    <?php if (!empty($store['store_image'])): ?>
                        <img src="../images/<?= rawurlencode($store['store_image']) ?>" class="card-photo">
                    <?php else: ?>
                        <span class="text-secondary small"><span class="bi bi-shop me-1"></span>店舗写真</span>
                    <?php endif; ?>
                </div>
                <!-- 1-2. 右側・店舗詳細枠 -->
                <div class="col-12 col-md-7 card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-leaf">営業中</span> <!-- MVPでは店舗の定休日を設けないため、装飾用の固定バッジとして扱う -->
                        <span class="small text-secondary"><?= htmlspecialchars($store['store_genre']) ?></span>
                    </div>
                    <h4 class="fw-bold mb-2"><?= htmlspecialchars($store['store_name']) ?></h4>
                    <p class="small text-secondary mb-3"><?= htmlspecialchars($store['store_description'] ?? '紹介文はありません') ?></p>
                    <table class="table table-sm table-borderless small mb-0">
                        <tbody>
                            <tr>
                                <th class="text-secondary" style="width: 100px;">住所</th>
                                <td><?= htmlspecialchars($store['address']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-secondary">営業時間</th>
                                <td><?= htmlspecialchars(substr($store['opening_time'], 0, 5)) ?> ~ <?= htmlspecialchars(substr($store['closing_time'], 0, 5)) ?></td>
                            </tr>
                            <tr>
                                <th class="text-secondary">電話番号</th>
                                <td><?= htmlspecialchars($store['tel'] ?? '未登録') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- 2. 当該店舗の出品中商品一覧 -->
    <section class="mb-4">
        <h5 class="fw-bold mb-3" id="storeProductsHeading">この店舗の出品中商品（<?= count($store_product_list) ?>件）</h5>
        <!-- /components/product_carousel.php に渡す情報の準備 -->
        <?php
        $carousel_product_list = $store_product_list;
        $carousel_empty_message = '現在、表示できる商品はありません。';
        $carouselId = 'storeProductCarousel';
        require __DIR__ . '/components/product_carousel.php';
        ?>
    </section>

<?php else: ?>
    <h3>店舗が見つかりません</h3>
    <p class="text-secondary">店舗情報を確認できませんでした。</p>
<?php endif; ?>

<!-- 3. 戻るボタン -->
<div class="text-center mt-3">
    <a href="<?= htmlspecialchars($homeUrl) ?>" class="btn btn-outline-brown btn-sm px-4">&larr; ホームへ戻る</a>
</div>