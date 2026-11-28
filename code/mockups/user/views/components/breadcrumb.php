<?php
// $breadcrumbs: [['label' => 'ホーム', 'url' => 'index.php'], ['label' => '現在のページ']] 
?>
<nav class="mb-3">
    <ol class="breadcrumb small mb-0">
        <?php foreach ($breadcrumbs as $key => $breadcrumb): ?>
            <?php if ($key === array_key_last($breadcrumbs)): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($breadcrumb['label']) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item">
                    <a href="<?= htmlspecialchars($breadcrumb['url']) ?>"><?= htmlspecialchars($breadcrumb['label']) ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>