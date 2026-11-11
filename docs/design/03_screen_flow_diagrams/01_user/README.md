# 一般利用者側 MVP 開発ロードマップ

作成日：2026-11-11

最後更新：2026-11-11

## 1. 目的・決定事項

一般利用者側の開発範囲と進捗を共有し、MVP完成後の開発にも備える。

2026-11-11のグループ討議で、最低限残す機能を次の4つに決定した。

- 商品を見る
- カートに入れる
- 注文・購入する（デモ決済）
- 過去の注文履歴を確認する

画面遷移図一覧：
![メインフロー v3](./v3/(mvp)main_user_flow_v3.png)
![共通レイアウト v3](./v3/(mvp)common_layout_v3.png)

## 2. MVP の画面・コンポーネントと進捗

状態：**未**＝未着手、**仮**＝モック・一部実装、**済**＝MVPの完成条件を満たす、**ー**＝対象外

PHPはHTMLの出力を含む。共通CSS・JSを使う場合、画面ごとの独立ファイルは不要（対象外）

| 画面・コンポーネント | ワイヤーフレーム | HTML・PHP | CSS | JS | データ連携 | 現状・次の作業 |
|---|---|---|---|---|---|---|
| 共通ヘッダー・フッター／カート・アカウントドロワー | [共通 v4](../../02_wireframes/01_user/00_common/v4/README.md) | 仮 | 仮 | 仮 | 仮 | 通コンポーネントあり。MVPのメニューに整理 |
| ホーム | [全体 v4](../../02_wireframes/01_user/01_homepage/v4/README.md) | 仮 | 仮 | 仮 | 仮 | JSONデータを使用。次は実データと接続 |
| 商品カード・店舗カード | [カード v5](../../02_wireframes/01_user/01_homepage/v5/README.md) | 仮 | 仮 | 仮 | 仮 | JSONデータを使用。次は実データと接続 |
| 商品一覧 | [v4](../../02_wireframes/01_user/02_product_listing/v4/README.md) | 未 | 未 | 未 | 未 | 一覧ページを作成 |
| 店舗一覧 | [v4](../../02_wireframes/01_user/03_store_listing/v4/README.md) | 未 | 未 | 未 | 未 | 一覧ページを作成 |
| 店舗詳細 | [v4](../../02_wireframes/01_user/05_store_detail/v4/README.md) | 未 | 未 | 未 | 未 | 店舗情報・商品を表示 |
| ログイン | [v4](../../02_wireframes/01_user/12_login/v4/README.md) | 未 | 未 | 未 | 未 | 実際の認証を実装 |
| 新規登録 | [v3](../../02_wireframes/01_user/13_signup/v3/README.md) | 未 | 未 | 未 | 未 | 実際の会員登録を実装 |
| カート全体・店舗別詳細 | [v3](../../02_wireframes/01_user/10_cart_checkout/v3/README.md) | 未 | 未 | 未 | 未 | 数量変更・削除、店舗ごとの注文導線を実装 |
| 注文確認・デモ決済・注文完了 | [v3](../../02_wireframes/01_user/10_cart_checkout/v3/README.md) | 未 | 未 | 未 | 未 | 注文を保存し、デモ決済後に完了画面を表示 |
| 注文履歴・注文詳細/受取画面 | [v4](../../02_wireframes/01_user/11_order_history/v4/README.md) | 未 | 未 | 未 | 未 | 注文を表示し、店舗側の受取確認と連動 |

## 3. MVP の完成条件

- [ ] 新規登録・ログインができ、自分の注文を確認できる
- [ ] ホーム・商品一覧・店舗一覧・店舗詳細四ケ所から商品を確認できる
- [ ] 商品をカートに追加し、数量変更・削除ができる
- [ ] 注文内容を確認し、デモ決済で注文を確定できる
- [ ] 注文完了後、注文履歴と注文詳細・受取画面を確認できる
- [ ] 注文後は店舗運営者側の受取確認と連動し、店舗側の受取確認が注文状態に反映される
- [ ] 空のカートやデータ取得失敗など、必要な状態を確認できる

## 4. 後回しの機能

| 機能 | 参照 | 扱い |
|---|---|---|
| 地図検索 | [v4](../../02_wireframes/01_user/15_map_search/v4/README.md) | MVP対象外 |
| お気に入り | [v4](../../02_wireframes/01_user/08_favorites/v4/README.md) | MVP対象外 |
| 通知・通知一覧 | [v4](../../02_wireframes/01_user/16_notifications/v4/README.md) | MVP対象外 |
| マイページ、ポイント・特典、ミニゲーム、植樹、応援 | [マイページ v4](../../02_wireframes/01_user/06_mypage/v4/README.md)／[ポイント v4](../../02_wireframes/01_user/20_point_detail/v4/README.md)／[旧フロー v2](./v2/%28編集用%29user_flow_review_mypage_v2.drawio) | MVP対象外 |
| ※ フッター（利用規約・プライバシーポリシー・運営について） | [規約](../../02_wireframes/01_user/17_terms/v4/README.md)／[プライバシー](../../02_wireframes/01_user/18_privacy/v4/README.md)／[運営](../../02_wireframes/01_user/19_about/v4/README.md) | MVPでは表示のみ、リンク先へ遷移させない |
| FAQ・ガイド、お問い合わせ | [FAQ・ガイド](../../02_wireframes/01_user/07_faq/v4/README.md)／[お問い合わせ](../../02_wireframes/01_user/09_contact/v3/README.md) | 後回し候補 |
| アカウント設定、退会、パスワード再設定 | [設定](../../02_wireframes/01_user/21_account_setting/v4/README.md)／[退会](../../02_wireframes/01_user/14_withdrawal/v3/README.md)／[ログイン](../../02_wireframes/01_user/12_login/v4/README.md) | 後回し候補 |
| レビュー・領収書 | [注文履歴 v4](../../02_wireframes/01_user/11_order_history/v4/README.md) | 後回し候補 |

## 5. 更新記録・次の作業

| 日付 | 内容 |
|---|---|
| 2026-11-11 | グループ討議の結果を反映し、MVPの範囲と現状を整理 |

次は、簡略化したMVPに合わせて、チーム共通の参考用`user_template.html`を作成する
