// ユーザー/js/auth-state.js
// ログイン状態判定と共通UI（ヘッダー・バナー・フッター・ドロワー・カード描画・操作）の統合スクリプト
// 登录状态判断与公共UI（顶栏・轮播横幅・页脚・双抽屉・卡片渲染・交互事件）统一脚本

// デフォルトのダミーデータ（PHP化後は引数でDB配列を渡すだけで上書き可能）
// 默认模拟数据（后续改用 PHP 后只需传参即可自动覆盖，HTML 无需修改）
const DEFAULT_PRODUCTS = [
     { name: '北海道産バターのクロワッサン', shop: 'ベーカリー太陽 新宿店', oldPrice: 300, price: 180, limit: '20:00', stock: 3, desc: '北海道産発酵バターを使用した当日焼き上げクロワッサン', amount: '2個入り（約120g）', allergen: '小麦・乳成分・卵', exp: '当日 22:00（消費期限）', storage: '常温保存' },
     { name: '彩り幕の内弁当', shop: 'キッチンみどり 西新宿店', oldPrice: 960, price: 480, limit: '20:30', stock: 2, desc: '焼き鮭・厚焼き玉子・季節野菜の煮物を詰めた手作り弁当', amount: '1折（約450g）', allergen: '小麦・卵・さけ・大豆', exp: '当日 22:00（消費期限）', storage: '10℃以下で冷蔵保存' },
     { name: '季節のフルーツタルト', shop: 'パティスリー森の風', oldPrice: 800, price: 520, limit: '19:30', stock: 0, desc: '旬のフルーツを使った自家製タルト', amount: '1カット', allergen: '小麦・卵・乳成分', exp: '当日 21:00', storage: '要冷蔵' }
];

const DEFAULT_SHOPS = [
     { name: 'ベーカリー太陽 新宿店', genre: 'ベーカリー', address: '東京都新宿区西新宿1-1-1' },
     { name: 'キッチンみどり 西新宿店', genre: 'お弁当・惣菜', address: '東京都新宿区西新宿2-4-1' },
     { name: 'パティスリー森の風', genre: 'スイーツ・洋菓子', address: '東京都新宿区新宿3-15-8' },
     { name: 'こもれびベーカリー', genre: 'ベーカリー', address: '東京都新宿区百人町1-10-5' },
     { name: '和菓子処 さくら庵', genre: 'スイーツ・洋菓子', address: '東京都新宿区新宿2-8-3' },
     { name: 'デリ＆サラダ フレッシュ', genre: 'お弁当・惣菜', address: '東京都新宿区百人町2-1-4' },
     { name: 'ブーランジェリー青空', genre: 'ベーカリー', address: '東京都新宿区西新宿7-9-12' },
     { name: '手作り惣菜 おふくろ亭', genre: 'お弁当・惣菜', address: '東京都新宿区大久保1-15-2' }
];

document.addEventListener('DOMContentLoaded', function () {
     const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
     const page = location.pathname.split('/').pop();
     const homeHref = isLoggedIn ? 'home.html' : 'index.html';

     // 1. 共通ヘッダーの自動生成 / 自动生成公共顶部导航栏
     const headerEl = document.getElementById('commonHeader');
     if (headerEl) {
          const rightIcons = isLoggedIn
               ? `<button type="button" class="btn btn-outline-secondary btn-sm position-relative" title="カート" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer">
                       <i class="bi bi-cart"></i>
                       <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-leaf" id="cartBadge" style="font-size: 0.65rem;">2</span>
                  </button>
                  <button type="button" class="btn btn-outline-brown btn-sm" title="マイアカウント" data-bs-toggle="offcanvas" data-bs-target="#accountDrawer">
                       <i class="bi bi-person-fill"></i>
                  </button>`
               : `<button type="button" class="btn btn-outline-secondary btn-sm" title="カート" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer">
                       <i class="bi bi-cart"></i>
                  </button>
                  <button type="button" class="btn btn-outline-brown btn-sm" title="マイアカウント" data-bs-toggle="offcanvas" data-bs-target="#accountDrawer">
                       <i class="bi bi-person"></i>
                  </button>`;

          headerEl.innerHTML = `
          <header class="navbar navbar-expand-lg py-2">
               <div class="container">
                    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="${homeHref}">
                         <img src="../images/logo-small.png" alt="JEC" style="height: 32px;">
                         <span>JEC Food Rescue</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topMenu">
                         <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center" id="topMenu">
                         <ul class="navbar-nav gap-2">
                              <li class="nav-item"><a class="nav-link ${page === 'home.html' || page === 'index.html' ? 'active fw-bold' : ''}" href="${homeHref}">ホーム</a></li>
                              <li class="nav-item"><a class="nav-link ${page === 'products.html' ? 'active fw-bold' : ''}" href="products.html">商品一覧</a></li>
                              <li class="nav-item"><a class="nav-link ${page === 'shops.html' || page === 'shop_detail.html' ? 'active fw-bold' : ''}" href="shops.html">店舗一覧</a></li>
                         </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2">${rightIcons}</div>
               </div>
          </header>`;
     }

     // 1.5 共通ヒーローバナー（2枚スライド）の自動生成 / 自动生成公共顶部轮播横幅
     const heroEl = document.getElementById('commonHero');
     if (heroEl) {
          const slides = isLoggedIn ? [
               { bg: '#EFE8DC', tag: 'フードロス削減', title: 'まだ美味しく食べられる食品を、お得にレスキュー！', desc: '閉店前のパンやお弁当を特別価格で予約・店頭受取できます。', btns: '<a href="products.html" class="btn btn-brown btn-sm px-4">出品中の商品を見る</a>' },
               { bg: '#E8EFE6', tag: '地元のお店を応援', title: '新宿エリアの人気ベーカリー・お惣菜店が続々参加中！', desc: 'お気に入りのお店を見つけて、今日の夕食や明日の朝食をお得に予約しよう。', btns: '<a href="shops.html" class="btn btn-brown btn-sm px-4">加盟店舗一覧を見る</a>' }
          ] : [
               { bg: '#EFE8DC', tag: 'フードロス削減', title: 'お店の美味しい食品を、お得にレスキューしよう！', desc: '会員登録（無料）すると、気になる商品を予約して店舗で受け取ることができます。', btns: '<a href="login/login.html" class="btn btn-brown btn-sm px-4">ログイン</a><a href="login/register.html" class="btn btn-outline-brown btn-sm px-4">新規会員登録</a>' },
               { bg: '#E8EFE6', tag: 'かんたん予約・店頭受取', title: '閉店前のパンやお弁当が最大50%OFF！', desc: 'スマホで事前に取り置き予約して、お仕事や学校帰りにスマートに受け取れます。', btns: '<a href="products.html" class="btn btn-brown btn-sm px-4">出品中の商品を見る</a><a href="shops.html" class="btn btn-outline-brown btn-sm px-4">加盟店舗一覧</a>' }
          ];

          heroEl.innerHTML = `
          <div id="commonCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
               <div class="carousel-indicators">
                    <button type="button" data-bs-target="#commonCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#commonCarousel" data-bs-slide-to="1"></button>
               </div>
               <div class="carousel-inner rounded-3 border">
                    ${slides.map((s, i) => `
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                         <div class="p-4 p-md-5 text-center" style="background-color: ${s.bg};">
                              <span class="badge bg-leaf mb-2">${s.tag}</span>
                              <h${i + 1} class="h4 fw-bold mb-2">${s.title}</h${i + 1}>
                              <p class="small text-secondary mb-3">${s.desc}</p>
                              <div class="d-flex justify-content-center gap-2">${s.btns}</div>
                         </div>
                    </div>`).join('')}
               </div>
               <button class="carousel-control-prev" type="button" data-bs-target="#commonCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
               </button>
               <button class="carousel-control-next" type="button" data-bs-target="#commonCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
               </button>
          </div>`;
     }

     // 2. 共通フッター・左右ドロワーの自動生成 / 自动生成公共页脚与侧边双抽屉
     const footerEl = document.getElementById('commonFooter');
     if (footerEl) {
          const accountDrawerBody = isLoggedIn
               ? `<div>
                       <div class="card mb-3"><div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2">
                                 <i class="bi bi-person-circle fs-2 text-secondary"></i>
                                 <div class="fw-bold">User Name</div>
                            </div>
                       </div></div>
                       <div class="list-group mb-3">
                            <a href="mypage.html" class="list-group-item list-group-item-action d-flex justify-content-between"><span>マイページ</span><span>&gt;</span></a>
                            <a href="orders.html" class="list-group-item list-group-item-action d-flex justify-content-between"><span>注文履歴</span><span>&gt;</span></a>
                          <!-- <a href="settings.html" class="list-group-item list-group-item-action d-flex justify-content-between"><span>設定</span><span>&gt;</span></a> -->
                       </div>
                  </div>
                  <div class="border-top pt-3 text-end">
                       <a href="index.html" class="text-decoration-none text-dark fw-bold small js-logout-btn"><i class="bi bi-box-arrow-right me-1"></i>ログアウト</a>
                  </div>`
               : `<div>
                       <div class="card text-center mb-3"><div class="card-body p-4">
                            <i class="bi bi-person-circle fs-1 text-secondary"></i>
                            <div class="fw-bold mt-2 mb-2">ゲストユーザー</div>
                            <p class="small text-secondary mb-3">ログインすると、便利な会員機能をご利用いただけます</p>
                            <div class="d-grid gap-2">
                                 <a href="login/login.html" class="btn btn-brown">ログイン</a>
                                 <a href="login/register.html" class="btn btn-outline-brown">新規会員登録</a>
                            </div>
                       </div></div>
                  </div>
                  <div class="border rounded p-3 bg-light small text-secondary">※マイページ・注文履歴等の利用にはログインが必要です。</div>`;

          // カートドロワーの中身（画像なしシンプル版）
          // 购物车抽屉内容（无图简洁版）
          const cartDrawerBody = isLoggedIn
               ? `<p class="small text-secondary mb-3">店舗ごとに内容の確認・予約注文ができます（1店舗につき合計10点まで）。</p>
                  <div class="flex-grow-1 overflow-auto">
                       <!-- 店舗① -->
                       <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                 <span class="fw-bold small"><i class="bi bi-shop me-1"></i>ベーカリー太陽 新宿店</span>
                                 <span class="badge bg-leaf">1点</span>
                            </div>
                            <div class="card-body p-3">
                                 <div class="d-flex justify-content-between align-items-start">
                                      <div>
                                           <div class="fw-bold small">北海道産バターのクロワッサン</div>
                                           <div class="small text-secondary">販売終了：20:00まで</div>
                                           <div class="fw-bold small mt-1">¥180</div>
                                      </div>
                                      <button type="button" class="btn btn-link text-secondary p-0" title="削除" onclick="this.closest('.card').remove()">
                                           <i class="bi bi-trash"></i>
                                      </button>
                                 </div>
                                 <hr class="my-2">
                                 <div class="d-flex justify-content-between align-items-center mb-3">
                                      <div class="input-group input-group-sm" style="width: 100px;">
                                           <button class="btn btn-outline-secondary" type="button">-</button>
                                           <input type="text" class="form-control text-center bg-white" value="1" readonly>
                                           <button class="btn btn-outline-secondary" type="button">+</button>
                                      </div>
                                      <div class="fw-bold small">小計：¥180</div>
                                 </div>
                                 <a href="order_confirm.html" class="btn btn-brown btn-sm w-100 fw-bold py-2">この店舗の注文手続きへ</a>
                            </div>
                       </div>

                       <!-- 店舗② -->
                       <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                 <span class="fw-bold small"><i class="bi bi-shop me-1"></i>キッチンみどり 西新宿店</span>
                                 <span class="badge bg-leaf">1点</span>
                            </div>
                            <div class="card-body p-3">
                                 <div class="d-flex justify-content-between align-items-start">
                                      <div>
                                           <div class="fw-bold small">彩り幕の内弁当</div>
                                           <div class="small text-secondary">販売終了：20:00まで</div>
                                           <div class="fw-bold small mt-1">¥480</div>
                                      </div>
                                      <button type="button" class="btn btn-link text-secondary p-0" title="削除" onclick="this.closest('.card').remove()">
                                           <i class="bi bi-trash"></i>
                                      </button>
                                 </div>
                                 <hr class="my-2">
                                 <div class="d-flex justify-content-between align-items-center mb-3">
                                      <div class="input-group input-group-sm" style="width: 100px;">
                                           <button class="btn btn-outline-secondary" type="button">-</button>
                                           <input type="text" class="form-control text-center bg-white" value="1" readonly>
                                           <button class="btn btn-outline-secondary" type="button">+</button>
                                      </div>
                                      <div class="fw-bold small">小計：¥480</div>
                                 </div>
                                 <a href="order_confirm.html" class="btn btn-brown btn-sm w-100 fw-bold py-2">この店舗の注文手続きへ</a>
                            </div>
                       </div>
                  </div>
                  <div class="border-top pt-3 mt-2">
                       <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-secondary">合計（2店舗・2点）</span>
                            <span class="fw-bold fs-6">¥660</span>
                       </div>
                       <a href="cart.html" class="btn btn-outline-dark btn-sm w-100 mb-2">カート画面を開く</a>
                       <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-dismiss="offcanvas">買い物を続ける</button>
                  </div>`
               : `<div>
                       <p class="small text-secondary mb-3">ログインしてご利用ください。商品をカートに追加し、店舗ごとに注文ができます。</p>
                       <div class="card text-center mb-3"><div class="card-body p-4">
                            <i class="bi bi-person-fill fs-1 text-secondary"></i>
                            <div class="fw-bold mt-2 mb-3">ゲストユーザー</div>
                            <div class="d-grid gap-2">
                                 <a href="login/login.html" class="btn btn-brown">ログイン</a>
                                 <a href="login/register.html" class="btn btn-outline-brown">新規会員登録</a>
                            </div>
                       </div></div>
                  </div>
                  <div class="border rounded p-3 bg-light small text-secondary">※カート機能・予約注文のご利用にはログインが必要です。</div>`;

          footerEl.innerHTML = `
          <footer class="bg-white border-top py-3 mt-4">
               <div class="container text-center small text-secondary">
                    <a href="#" class="text-decoration-none text-secondary me-2">利用規約</a> /
                    <a href="#" class="text-decoration-none text-secondary mx-2">プライバシーポリシー</a> /
                    <a href="#" class="text-decoration-none text-secondary ms-2">運営について</a>
                    <div class="mt-1">© 2026 JEC Food Rescue</div>
               </div>
          </footer>
          <div class="offcanvas offcanvas-end" tabindex="-1" id="accountDrawer">
               <div class="offcanvas-header border-bottom"><h5 class="offcanvas-title fw-bold">マイアカウント</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
               <div class="offcanvas-body d-flex flex-column justify-content-between">${accountDrawerBody}</div>
          </div>
          <div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
               <div class="offcanvas-header border-bottom"><h5 class="offcanvas-title fw-bold">カート</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
               <div class="offcanvas-body d-flex flex-column">${cartDrawerBody}</div>
          </div>`;
     }

     // 3. 共通クリックイベント処理（イベント委譲） / 统一点击交互事件委托
     document.body.addEventListener('click', function (e) {
          if (e.target.closest('.js-flip-to-back')) e.target.closest('.product-flip-card').classList.add('is-flipped');
          if (e.target.closest('.js-flip-to-front')) e.target.closest('.product-flip-card').classList.remove('is-flipped');
          if (e.target.closest('.btn-minus')) {
               const valSpan = e.target.closest('.qty-control').querySelector('.qty-val');
               const c = parseInt(valSpan.textContent, 10);
               if (c > 1) valSpan.textContent = c - 1;
          }
          if (e.target.closest('.btn-plus')) {
               const btn = e.target.closest('.btn-plus');
               const valSpan = btn.closest('.qty-control').querySelector('.qty-val');
               const max = parseInt(btn.dataset.max, 10) || 1;
               const c = parseInt(valSpan.textContent, 10);
               if (c < max) valSpan.textContent = c + 1;
          }
          if (e.target.closest('.js-add-cart')) {
               if (!isLoggedIn) {
                    new bootstrap.Offcanvas(document.getElementById('accountDrawer')).show();
                    return;
               }
               const qty = parseInt(e.target.closest('.card-body').querySelector('.qty-val')?.textContent || '1', 10);
               const badge = document.getElementById('cartBadge');
               if (badge) badge.textContent = parseInt(badge.textContent, 10) + qty;
               new bootstrap.Offcanvas(document.getElementById('cartDrawer')).show();
          }
          // 「[ もっと見る ▼ ]」クリックで残りの店舗を表示し、ボタンを非表示にする
          // 点击「[ もっと見る ▼ ]」展开剩余店铺，并将按钮本身隐藏
          if (e.target.closest('#toggleMoreBtn')) {
               document.querySelectorAll('.extra-shop-card').forEach(el => el.classList.remove('d-none'));
               e.target.closest('#toggleMoreBtn').classList.add('d-none');
          }
          if (e.target.closest('.js-logout-btn')) sessionStorage.removeItem('isLoggedIn');
          // カート画面・ドロワー内の数量 +/- ボタン（見た目だけ増減）
          if (e.target.closest('.input-group button')) {
               const btn = e.target.closest('button');
               const input = btn.parentElement.querySelector('input');
               let val = parseInt(input.value, 10) || 1;
               if (btn.textContent.trim() === '-' && val > 1) input.value = val - 1;
               if (btn.textContent.trim() === '+' && val < 10) input.value = val + 1;
          }
     });

     // 4. パンくず・ホームへ戻るリンク補正 / 修正面包屑与返回首页链接
     document.querySelectorAll('.breadcrumb a[href="index.html"], .js-home-link').forEach(a => a.setAttribute('href', homeHref));
});

// 5. 全ページ共通カード描画関数（デフォルトデータ内蔵） / 全站通用卡片渲染公共函数（内置默认数据）
window.renderProductCards = function (containerId, products = DEFAULT_PRODUCTS) {
     const el = document.getElementById(containerId);
     if (!el) return;
     el.innerHTML = products.map(p => {
          const soldout = p.stock === 0;
          return `
          <div class="col-12 col-md-4">
               <div class="product-flip-card ${soldout ? 'product-soldout' : ''}">
                    <div class="product-flip-inner">
                         <div class="card product-flip-front">
                              <div class="card-img-box border-bottom d-flex align-items-center justify-content-center">
                                   <span class="corner-ribbon">${soldout ? '売り切れ' : `残り：${p.stock}点`}</span>
                                   <span class="${soldout ? 'text-light' : 'text-secondary'} small"><i class="bi bi-image me-1"></i>商品写真</span>
                              </div>
                              <div class="card-body p-3 d-flex flex-column justify-content-between ${soldout ? 'soldout-disable' : ''}">
                                   <div>
                                        <h3 class="h6 fw-bold mb-1">${p.name}</h3>
                                        <div class="small ${soldout ? '' : 'text-secondary'} mb-1">${p.shop}</div>
                                        <div class="mb-1"><span class="small ${soldout ? '' : 'text-secondary'}">通常価格 <del>¥${p.oldPrice}</del></span> <span class="fw-bold mx-1">&gt;</span> <span class="fw-bold fs-5">¥${p.price}</span></div>
                                        <div class="small ${soldout ? '' : 'text-secondary'} mb-2">${p.limit}まで受取可</div>
                                   </div>
                                   <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                             <span class="small">購入数量</span>
                                             <div class="btn-group btn-group-sm qty-control">
                                                  <button type="button" class="btn btn-outline-secondary btn-minus" ${soldout ? 'disabled' : ''}>-</button>
                                                  <span class="btn btn-outline-secondary disabled text-dark qty-val">1</span>
                                                  <button type="button" class="btn btn-outline-secondary btn-plus" data-max="${p.stock}" ${soldout ? 'disabled' : ''}>+</button>
                                             </div>
                                        </div>
                                        <div class="d-grid mb-2"><button type="button" class="btn ${soldout ? 'btn-outline-secondary' : 'btn-outline-dark js-add-cart'} btn-sm" ${soldout ? 'disabled' : ''}><i class="bi bi-cart me-1"></i>カートに入れる</button></div>
                                        <div class="text-end">${soldout ? '<span class="small">商品について &rarr;</span>' : '<button type="button" class="flip-toggle-link js-flip-to-back">商品について &rarr;</button>'}</div>
                                   </div>
                              </div>
                         </div>
                         ${soldout ? '' : `
                         <div class="card product-flip-back">
                              <div class="card-body p-4 d-flex flex-column justify-content-between">
                                   <div>
                                        <h4 class="h6 fw-bold border-bottom pb-2 mb-3">${p.name}</h4>
                                        <ul class="small ps-3 mb-0" style="line-height: 1.9;">
                                             <li><strong>商品説明：</strong>${p.desc}</li>
                                             <li><strong>内容量：</strong>${p.amount}</li>
                                             <li><strong>アレルゲン：</strong>${p.allergen}</li>
                                             <li><strong>食品期限：</strong>${p.exp}</li>
                                             <li><strong>保存方法：</strong>${p.storage}</li>
                                        </ul>
                                   </div>
                                   <div class="text-end border-top pt-2"><button type="button" class="flip-toggle-link js-flip-to-front">商品に戻る &rarr;</button></div>
                              </div>
                         </div>`}
                    </div>
               </div>
          </div>`;
     }).join('');
};

window.renderSimpleShopCards = function (containerId, shops = DEFAULT_SHOPS, limit = 6) {
     const el = document.getElementById(containerId);
     if (!el) return;
     el.innerHTML = shops.map((s, idx) => `
          <div class="col-12 col-md-4 ${idx >= limit ? 'd-none extra-shop-card' : ''}">
               <div class="card h-100">
                    <div class="shop-img-box border-bottom d-flex align-items-center justify-content-center"><span class="text-secondary small"><i class="bi bi-shop me-1"></i>店舗写真</span></div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                         <div><div class="mb-1"><span class="fw-bold">${s.name}</span> <span class="small text-secondary ms-1">${s.genre}</span></div><div class="small text-secondary mb-2">${s.address}</div></div>
                         <div class="text-end"><a href="shop_detail.html" class="flip-toggle-link">[ 店舗詳細 &rarr; ]</a></div>
                    </div>
               </div>
          </div>`).join('');
};