<?php
// bannerで始まるjpg画像を取得する（DBから取得を検討）
$campaign_image_dir = __DIR__ . '/../../../images/';
$campaign_image_list = glob($campaign_image_dir . 'banner*.jpg') ?: [];
?>

<section class="mb-5">
    <div id="campaignCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner rounded-3 border shadow-sm">
            <?php if ($campaign_image_list === []): ?>
                <!-- 画像がない場合のプレースホルダー -->
                <div class="carousel-item active">
                    <div class="banner-slide bg-light"></div>
                </div>
            <?php else: ?>
                <?php foreach ($campaign_image_list as $index => $image): ?>
                    <div class="carousel-item<?= $index === 0 ? ' active' : '' ?>">
                        <img src="../images/<?= rawurlencode(basename($image)) ?>" class="banner-slide d-block w-100 object-fit-cover">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (count($campaign_image_list) > 1): ?>
            <button type="button" class="carousel-control-prev" data-bs-target="#campaignCarousel" data-bs-slide="prev">
                <span class="bg-dark bg-opacity-50 rounded-circle p-2 d-flex">
                    <span class="carousel-control-prev-icon"></span>
                </span>
            </button>
            <button type="button" class="carousel-control-next" data-bs-target="#campaignCarousel" data-bs-slide="next">
                <span class="bg-dark bg-opacity-50 rounded-circle p-2 d-flex">
                    <span class="carousel-control-next-icon"></span>
                </span>
            </button>
        <?php endif; ?>
    </div>
</section>