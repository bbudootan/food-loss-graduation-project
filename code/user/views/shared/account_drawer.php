<div class="offcanvas offcanvas-end" id="accountDrawer">
    <!-- ドロワーメニューのタイトル -->
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">マイアカウント</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <!-- ドロワーメニューの中身 -->
    <div class="offcanvas-body d-flex flex-column"><!-- d-flex flex-column: mt-autoで要素を下部に配置するため、コンテナ内の要素を縦方向に並べる -->
        <?php if ($member === null): ?>
            <p class="small text-secondary">ログインしてご利用ください<br>ログインすると、便利な会員機能をご利用いただけます</p>
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

            <div class="border-top pt-3 mt-auto">
                <p class="text-center">※ マイページ・注文履歴の利用にはログインが必要です。</p>
            </div>
        <?php else: ?>
            <div class="card text-center mb-4">
                <div class="card-body p-3">
                    <img src="../images/<?= rawurlencode($member['avatar_image']) ?>" width="72" height="72" class="rounded-circle object-fit-cover mb-3">
                    <div class="fw-bold mb-2"><?= escapeHtml($member['name']) ?></div>
                </div>
            </div>

            <ul class="list-group mb-5">
                <li class="list-group-item list-group-item-action"><a href="my_page.php" class="d-flex justify-content-between text-decoration-none text-body">マイページ <span class="bi bi-chevron-right"></span></a></li>
                <li class="list-group-item list-group-item-action"><a href="order_history.php" class="d-flex justify-content-between text-decoration-none text-body">注文履歴 <span class="bi bi-chevron-right"></span></a></li>
                <li class="list-group-item list-group-item-action"><a href="account_setting.php" class="d-flex justify-content-between text-decoration-none text-body">設定 <span class="bi bi-chevron-right"></span></a></li>
                <li class="list-group-item list-group-item-action" hidden><a href="favorites.php" class="d-flex justify-content-between text-decoration-none text-body">お気に入り <span class="bi bi-chevron-right"></span></a></li>
                <li class="list-group-item list-group-item-action" hidden><a href="point_detail.php" class="d-flex justify-content-between text-decoration-none text-body">ポイント <span class="bi bi-chevron-right"></span></a></li>
                <li class="list-group-item list-group-item-action" hidden><a href="faq.php" class="d-flex justify-content-between text-decoration-none text-body">ガイド・FAQ <span class="bi bi-chevron-right"></span></a></li>
                <li class="list-group-item list-group-item-action" hidden><a href="contact.php" class="d-flex justify-content-between text-decoration-none text-body">お問い合わせ <span class="bi bi-chevron-right"></span></a></li>
            </ul>

            <!-- ★★モックアップのプレビュー表示テスト用・実装後削除予定★★ -->
            <a href="index.php" class="btn btn-light d-block w-75 mx-auto mb-3">（モックアップ）ゲスト画面に戻る</a>

            <div class="border-top pt-3 mt-auto d-flex justify-content-end">
                <a href="logout.php" class="link-secondary gap-1 text-decoration-none">ログアウト <span class="bi bi-box-arrow-right"></span></a>
            </div>
        <?php endif; ?>
    </div>
</div>