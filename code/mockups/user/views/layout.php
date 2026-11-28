<!DOCTYPE HTML>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> | JEC Food Rescue</title> <!-- 各画面から渡されたページタイトル -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/user.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- 1. 共通ヘッダー -->
    <?php require __DIR__ . '/shared/header.php'; ?>

    <main class="container py-4 flex-grow-1">
        <!-- 2. パンくずリストが設定されている場合に表示 -->
        <?php if (!empty($breadcrumbs)): ?>
            <?php require __DIR__ . '/components/breadcrumb.php'; ?>
        <?php endif; ?>
        <!-- 3. 各画面から指定されたメインコンテンツ -->
        <?php require $pageView; ?>
    </main>

    <!-- 4. 共通フッター -->
    <?php require __DIR__ . '/shared/footer.php'; ?>

    <!-- 5. ドロワーメニュー -->
    <?php require __DIR__ . '/shared/account_drawer.php'; ?>
    <?php require __DIR__ . '/shared/cart_drawer.php'; ?>

    <!-- 6. Bootstrap JS・jQuery・自作JSの読込 -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="../js/product_card.js"></script>
    <script defer src="../js/homepage_store_list.js"></script>
</body>

</html>