# 一般利用者向けワイヤーフレーム

各画面の既存版は元の **v1 / v2 / v3** を維持し、対象が存在する版のみ保存します。

## 画面一覧

| 画面 | 保存済みのバージョン |
|---|---|| 共通レイアウト・ナビゲーション | [v2](./00_common/v2/README.md) / [v3](./00_common/v3/README.md) |
| ホームページ | [v1](./01_homepage/v1/README.md) / [v2](./01_homepage/v2/README.md) / [v3](./01_homepage/v3/README.md) |
| 商品一覧 | [v3](./02_product_listing/v3/README.md) |
| 店舗一覧 | [v3](./03_store_listing/v3/README.md) |
| 商品詳細 | [v3](./04_product_detail/v3/README.md) |
| 店舗詳細 | [v3](./05_store_detail/v3/README.md) |
| マイページ | [v1](./06_mypage/v1/README.md) / [v2](./06_mypage/v2/README.md) / [v3](./06_mypage/v3/README.md) |
| FAQ・ご利用ガイド | [v2](./07_faq/v2/README.md) / [v3](./07_faq/v3/README.md) |
| お気に入り | [v2](./08_favorites/v2/README.md) / [v3](./08_favorites/v3/README.md) |
| お問い合わせ | [v2](./09_contact/v2/README.md) / [v3](./09_contact/v3/README.md) |
| カート・注文・決済 | [v2](./10_cart_checkout/v2/README.md) / [v3](./10_cart_checkout/v3/README.md) |
| 注文履歴 | [v2](./11_order_history/v2/README.md) / [v3](./11_order_history/v3/README.md) |
| ログイン | [v2](./12_login/v2/README.md) / [v3](./12_login/v3/README.md) |
| 新規登録 | [v2](./13_signup/v2/README.md) / [v3](./13_signup/v3/README.md) |
| 退会 | [v3](./14_withdrawal/v3/README.md) |

## v3 設計資料の冒頭説明（原文）

ワイヤーフレームの作成では、Figmaやdraw.ioなどの作図ツールではなくASCIIテキストで作成しました。限られた開発リソースのなかで品質と効率を両立するため、AIコーディングエージェントの活用を前提とした設計プロセスを採用しています。

採用理由

1. コンピューターとの親和性
ASCIIで記述したレイアウトはコンピューターが容易に読み書きでき、SVGファイルへの簡単に転換、HTML・CSS・コンポーネントコードへ直接変換などは気軽にできます。設計段階への比較的多くの時間と労力の投資が、そのまま実装工程の効率化につながります。
这次不采用手绘而是在文字线框图的基础上利用了github的开源工具bob.svg直接从ASCII变换成SVG矢量图，好处多多。但bob.svg做的矢量图只能在它的网页上修改，导出文件后无法用draw.io等绘图工具进行图形编辑。

2. 低い反復コスト
要素の追加や配置変更はテキスト編集のみで数十秒で完了できます。作図ツールのような手作業でのレイアウト再構成が不要なため、仕様変更や方針転換が頻繁に起こる初期段階に適しています。安心して試行錯誤できる手法だと感じています。
