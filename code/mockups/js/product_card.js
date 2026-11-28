$('[data-product-card]').each(function () {
    const card = $(this);
    const limit = Math.min(10, Number(card.attr('data-stock')));
    const quantityOutput = card.find('[data-quantity]');
    const decrease = card.find('[data-quantity-step="-1"]');
    const increase = card.find('[data-quantity-step="1"]');

    // 1. 商品数量の増減：+／− ボタン押下時に表示数量を更新し、在庫数と購入上限に応じてボタンの有効・無効を切り替える
    card.find('[data-quantity-step]').on('click', function () {
        const requested = Number(quantityOutput.prop('value')) + Number($(this).attr('data-quantity-step'));
        const quantity = Math.min(limit, Math.max(1, requested));
        quantityOutput.prop('value', quantity);
        decrease.prop('disabled', quantity <= 1);
        increase.prop('disabled', quantity >= limit);
    });

    // 2. 商品カードの表・裏切り替え
    card.find('[data-flip-card]').on('click', function () {
        const showBack = $(this).attr('data-flip-card') === 'back';
        card.toggleClass('is-flipped', showBack);
        card.find('[data-card-front]').prop('inert', showBack);
        card.find('[data-card-back]').prop('inert', !showBack);
        card.find(showBack ? '[data-flip-card="front"]' : '[data-flip-card="back"]').trigger('focus');
    });
});

// ★TO-DO:
// 1. 在庫数はDBの取得結果から画面に渡し、JSで購入数量の増減を制御する
// 2. 「カートに入れる」押下時に商品IDと購入数量をサーバーへ送信し、PHPからDAOを呼び出してdbo.cartに保存
// 3. （検討中）画面表示後に在庫数が変動する可能性があるため、JS側の数量制限だけに頼らず、サーバー側でも最新の在庫数と購入数量を要チェック
