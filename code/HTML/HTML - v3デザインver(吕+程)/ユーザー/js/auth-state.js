// ユーザー/js/auth-state.js
// ログイン状態（未ログイン／ログイン済）を自動判定し、ヘッダー・バナー・ドロワー・共通操作を制御するスクリプト
// 自动判断登录状态（未登录／已登录），并统一控制顶部导航栏、轮播横幅、侧边抽屉及通用交互的脚本

document.addEventListener('DOMContentLoaded', function () {
     // sessionStorageからログイン状態を取得（PHP化後は $_SESSION['member'] で判定）
     // 从 sessionStorage 获取登录状态（后续改用 PHP 后改为 $_SESSION['member'] 判断）
     const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
     // 現在のHTMLファイル名を取得（ナビゲーションのアクティブ表示用）
     // 获取当前打开的 HTML 文件名（用于高亮顶部导航菜单的当前页）
     const page = location.pathname.split('/').pop();
     const homeHref = isLoggedIn ? 'home.html' : 'index.html';

     // =========================================================================
     // 1. 共通ヘッダーの自動生成 / 自动生成公共顶部导航栏
     // =========================================================================
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
                    <!-- 左側：ロゴとサイト名 / 左侧：Logo 与网站名称 -->
                    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="${homeHref}">
                         <img src="../images/logo-small.png" alt="JEC" style="height: 32px;">
                         <span>JEC Food Rescue</span>
                    </a>
                    <!-- スマホ用ハンバーガーボタン / 手机端折叠菜单按钮 -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topMenu">
                         <span class="navbar-toggler-icon"></span>
                    </button>
                    <!-- 中央：ナビゲーションリンク / 中间：页面导航菜单 -->
                    <div class="collapse navbar-collapse justify-content-center" id="topMenu">
                         <ul class="navbar-nav gap-2">
                              <li class="nav-item"><a class="nav-link ${page === 'home.html' \vert{}\vert{} page === 'index.html' ? 'active fw-bold' : ''}" href="${homeHref}">ホーム</a></li>
                              <li class="nav-item"><a class="nav-link ${page === 'products.html' ? 'active fw-bold' : ''}" href="products.html">商品一覧</a></li>
                              <li class="nav-item"><a class="nav-link ${page === 'shops.html' || page === 'shop_detail.html' ? 'active fw-bold' : ''}" href="shops.html">店舗一覧</a></li>
                         </ul>
                    </div>
                    <!-- 右側：アクションアイコン / 右侧：功能图标 -->
                    <div class="d-flex align-items-center gap-2">${rightIcons}</div>
               </div>
          </header>`;
     }

     // =========================================================================
     // 1.5 共通ヒーローバナー（2枚スライド）の自動生成 / 自动生成公共顶部轮播横幅（2张幻灯片可滑动）
     // =========================================================================
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

     // =========================================================================
     // 2. 共通フッター・左右ドロワーの自動生成 / 自动生成公共页脚与侧边抽屉
     // =========================================================================
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
                            <a href="settings.html" class="list-group-item list-group-item-action d-flex justify-content-between"><span>設定</span><span>&gt;</span></a>
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

          const cartDrawerBody = isLoggedIn
               ? `<div>
                       <p class="small text-secondary mb-3">店舗ごとに内容の確認・予約注文ができます。</p>
                       <div class="card mb-3">
                            <div class="card-header bg-light py-2 d-flex justify-content-between">
                                 <span class="fw-bold small"><i class="bi bi-shop me-1"></i>ベーカリー太陽 新宿店
