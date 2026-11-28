<?php
// 呼び出し元から $carousel_product_list・$carouselId・$carousel_empty_message を受け取る。
// スマホは1件、中画面は2件、大画面は4件ずつ表示する。
?>
<?php if ($carousel_product_list === []): ?>
    <p class="text-secondary"><?= htmlspecialchars($carousel_empty_message) ?></p>
<?php else: ?>
    <?php foreach (['mobile' => 1, 'tablet' => 2, 'desktop' => 4] as $layout => $itemsPerSlide): ?>
        <?php
        $slides = array_chunk($carousel_product_list, $itemsPerSlide);
        $instanceId = $carouselId . '-' . $layout;
        ?>
        <?php
        $visibilityClasses = [
            'mobile' => 'd-md-none',
            'tablet' => 'd-none d-md-block d-lg-none',
            'desktop' => 'd-none d-lg-block',
        ];
        $visibilityClass = $visibilityClasses[$layout];
        ?>
        <div class="<?= $visibilityClass ?>">
            <div id="<?= htmlspecialchars($instanceId) ?>" class="carousel slide position-relative" data-bs-interval="false">
                <div class="carousel-inner">
                    <?php foreach ($slides as $slideIndex => $productsOnSlide): ?>
                        <div class="carousel-item<?= $slideIndex === 0 ? ' active' : '' ?>">
                            <div class="row g-3">
                                <?php foreach ($productsOnSlide as $product): ?>
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <?php require __DIR__ . '/product_card.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($slides) > 1): ?>
                    <button type="button" class="slider-nav-btn slider-nav-prev"
                        data-bs-target="#<?= htmlspecialchars($instanceId) ?>" data-bs-slide="prev">
                        <span class="bi bi-chevron-left"></span>
                    </button>
                    <button type="button" class="slider-nav-btn slider-nav-next"
                        data-bs-target="#<?= htmlspecialchars($instanceId) ?>" data-bs-slide="next">
                        <span class="bi bi-chevron-right"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>