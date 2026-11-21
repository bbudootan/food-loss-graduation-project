<header class="sticky-top">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container position-relative">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= escapeHtml($homeUrl) ?>">
                <img src="../images/logo-small.png" height="32">
                <span>JEC Food Rescue</span>
            </a>

            <div class="d-flex align-items-center gap-1 order-lg-last">
                <!-- カートドロワー（カート内商品の合計数量をバッジで表示） -->
                <button type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer">
                    <span class="position-relative d-inline-flex">
                        <span class="bi bi-cart3"></span>
                        <?php if ($cartQuantity > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-ash ms-1 mt-n1">
                                <?= $cartQuantity ?>
                            </span>
                        <?php endif; ?>
                    </span>
                </button>
                <!-- アカウントドロワー -->
                <button type="button" class="btn position-relative" data-bs-toggle="offcanvas" data-bs-target="#accountDrawer">
                    <span class="bi bi-person"></span>
                </button>
                <!-- モバイル画面幅でのレスポンシブ対応を適用 -->
                <button type="button" class="navbar-toggler d-lg-none" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#topMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse justify-content-center header-nav-center" id="topMenu">
                <ul class="navbar-nav gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="<?= escapeHtml($homeUrl) ?>">ホーム</a></li>
                    <li class="nav-item"><a class="nav-link" href="product_listing.php">商品一覧</a></li>
                    <li class="nav-item"><a class="nav-link" href="store_listing.php">店舗一覧</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>