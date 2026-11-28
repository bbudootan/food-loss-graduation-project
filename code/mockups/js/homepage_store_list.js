document.addEventListener('DOMContentLoaded', function () {
        $('#showMoreStores').on('click', function () {
            // 非表示の店舗を最大3件表示する
            $('.store-item.d-none').slice(0, 3).removeClass('d-none');

            // すべて表示したらボタンを隠す
            if ($('.store-item.d-none').length === 0) {
                $(this).hide();
            }
        });
    });