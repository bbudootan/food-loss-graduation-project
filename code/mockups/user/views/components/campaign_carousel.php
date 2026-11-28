<?php
// bannerで始まるjpg画像を取得する（MVPでは静的コンテンツとして扱う）
$campaign_image_dir = __DIR__ . '/../../../images/';
// glob(): 指定したパターンに一致するファイルのパスを取得
$campaign_image_list = glob($campaign_image_dir . 'banner*.jpg') ?: [];
?>

<section class="mb-5">
    <div id="campaignCarousel" class="carousel slide" data-bs-ride="carousel">
        <!-- 1. スライド画像の表示部分 -->
        <div class="carousel-inner rounded-3 border shadow-sm">
            <?php if ($campaign_image_list === []): ?>
                <!-- 画像がない場合のプレースホルダー -->
                <div class="carousel-item active">
                    <div class="banner-slide bg-light"></div>
                </div>
            <?php else: ?>
                <?php foreach ($campaign_image_list as $index => $image): ?>
                    <div class="carousel-item<?= $index === 0 ? ' active' : '' ?>">
                        <img src="../images/<?= rawurlencode(basename($image)) ?>" class="banner-slide d-block w-100 object-fit-cover"> <!-- basename(): パスからファイル名だけを取り出す、rawurlencode(): ファイル名の空白や記号をURL用に変換  -->
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (count($campaign_image_list) > 1): ?>
            <!-- 2. スライド位置を示すインジケーター -->
            <div class="carousel-indicators">
                <?php foreach ($campaign_image_list as $index => $image): ?>
                    <button type="button" data-bs-target="#campaignCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>"></button>
                <?php endforeach; ?>
            </div>
            <!-- 3. 前のスライドに戻るボタン -->
            <button type="button" class="carousel-control-prev" data-bs-target="#campaignCarousel" data-bs-slide="prev">
                <span class="bg-dark bg-opacity-50 rounded-circle p-2 d-flex">
                    <span class="carousel-control-prev-icon"></span>
                </span>
            </button>
            <!-- 4. 次のスライドに進むボタン -->
            <button type="button" class="carousel-control-next" data-bs-target="#campaignCarousel" data-bs-slide="next">
                <span class="bg-dark bg-opacity-50 rounded-circle p-2 d-flex">
                    <span class="carousel-control-next-icon"></span>
                </span>
            </button>
        <?php endif; ?>
    </div>
</section>