<div class="offcanvas offcanvas-end" id="cartDrawer">
    <!-- ドロワーメニューのタイトル -->
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">カート</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <!-- ドロワーメニューの中身 -->
    <div class="offcanvas-body">
        <?php if ($member === null): ?>
            <p class="small text-secondary">ログインしてご利用ください<br>商品をカートに追加し、店舗ごとに内容の確認・注文ができます</p>
            <div class="card text-center mb-4">
                <div class="card-body p-3">
                    <span class="bi bi-person-circle fs-1 text-secondary"></span>
                    <div class="fw-bold mb-3">ゲストユーザー</div>
                    <!-- 遷移先は未作成 -->
                    <a href="login.php" class="btn btn-brown d-block w-75 mx-auto mb-3">ログイン</a>
                    <a href="user_signup.php" class="btn btn-brown d-block w-75 mx-auto mb-3">新規会員登録</a>
                </div>
            </div>

            <!-- ★★モックアップのプレビュー表示テスト用・実装後削除予定★★ -->
            <a href="index.php?preview=member" class="btn btn-light d-block w-75 mx-auto mb-3">（モックアップ）会員画面をプレビュー</a>

        <?php else: ?>
            <p class="small text-secondary">店舗ごとに注文・お支払いを行います<br>1店舗につき合計10点まで追加できます<br>一度に最大3店舗まで選択できます</p>
            <?php if ($cartStores === []): ?>
                <p class="mx-auto">カートに商品が入っていません</p>
            <?php endif; ?>

            <?php foreach ($cartStores as $cartStore): ?>
                <section class="card mb-3 shadow-sm overflow-hidden">
                    <!-- 店舗名とカート内点数 -->
                    <div class="card-header bg-white d-flex align-items-center justify-content-between gap-2 px-3 py-3">
                        <h6 class="fw-bold mb-0"><span class="bi bi-shop-window me-2"></span><?= htmlspecialchars($cartStore['name']) ?></h6>
                        <span class="badge rounded-pill text-bg-light border text-secondary flex-shrink-0"><?= $cartStore['quantity'] ?> / 10点</span>
                    </div>
                    <!-- 商品名・単価・カート内数量 -->
                    <ul class="list-unstyled px-3 pt-3 mb-0">
                        <?php foreach ($cartStore['items'] as $cartProduct): ?>
                            <li class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                <span class="small"><?= htmlspecialchars($cartProduct['goods_name']) ?></span>
                                <span class="small text-secondary text-nowrap">¥<?= number_format($cartProduct['selling_price']) ?> × <?= $cartProduct['cart_quantity'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <!-- 店舗ごとの小計 -->
                    <div class="card-body px-3 pt-0 pb-3">
                        <div class="d-flex justify-content-between align-items-baseline border-top pt-3 mb-1">
                            <span class="small text-secondary">店舗小計</span>
                            <span class="fw-bold fs-5">¥<?= number_format($cartStore['subtotal']) ?></span>
                        </div>
                        <p class="small text-secondary mb-3"><span class="bi bi-clock me-1"></span><?php if ($cartStore['pickup_deadline'] !== null): ?><?= $cartStore['pickup_deadline']->format('n/j H:i') ?> まで受取可能<?php else: ?>受取期限を確認できません<?php endif; ?></p>
                        <div class="d-flex gap-2">
                            <a href="cart.php" class="btn btn-outline-brown btn-sm w-50 py-2">内容を変更</a>
                            <a href="cart_checkout.php" class="btn btn-brown btn-sm w-50 py-2">注文手続きへ</a>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>

            <div class="border-top pt-3">
                <?php if ($cartStores !== []): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-secondary">合計（<?= count($cartStores) ?>店舗・<?= $cartQuantity ?>点）</span>
                        <span class="fw-bold fs-5">¥<?= number_format($cartTotal) ?></span>
                    </div>
                <?php endif; ?>

                <!-- ★★モックアップのプレビュー表示テスト用・実装後削除予定★★ -->
                <div class="d-grid mt-5">
                    <a href="index.php" class="btn btn-light d-block w-75 mx-auto mb-3">（モックアップ）ゲスト画面に戻る</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>