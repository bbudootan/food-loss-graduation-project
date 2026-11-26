# テーブル定義書 v4（MVP版）

更新日：2026-11-26

MVP（簡略化版）の範囲に合わせて、テーブルと列を減らした版です。変更の詳細は [CHANGELOG.md](../CHANGELOG.md)、決めたルールは [DECISIONS.md](../DECISIONS.md) を見てください。

## テーブル（11個）

| テーブル名 | 日本語名 | 主に使う画面 |
|---|---|---|
| account | アカウント | ログイン、新規登録 |
| store | 店舗 | 店舗一覧・店舗詳細、店舗メイン、店舗管理 |
| category | 商品カテゴリ | 商品マスター、商品一覧の絞り込み |
| goods_master | 商品マスター | 商品マスター、商品カード |
| listing | 出品 | 出品・在庫、商品カード・商品一覧 |
| cart | カート | カートドロワー・カート全体 |
| orders | 注文 | 注文確認・デモ決済、注文履歴、注文管理、受け取り |
| order_detail | 注文明細 | 注文詳細、注文管理 |
| open_store_application | 出店申請 | 新規出店申請、新規出店申請の管理 |
| goods_registration_request | 商品登録依頼 | 商品登録依頼（店舗は写真を送るだけ・管理者） |
| goods_registration_request_photo | 商品登録依頼写真 | 商品登録依頼（写真で依頼） |

## v3からのおもな変更

- MVPで使わないテーブルを外した（カード、レビュー、レビュー通報、お気に入り商品・店舗、FAQ、お問い合わせ、店舗削除申請、ポイント履歴、苗の履歴）
- 列を削除した
  - account：point_balance、seedling_balance
  - store：closed_days（定休日はなく、毎日営業する前提）
  - listing：listing_description
  - orders：cancel_date、cancel_reason、used_point（cancel_date は11/16に戻した）
- orders.order_status から「3=取り消し」を削除した（11/16に「3=キャンセル（店舗都合）」として戻した）
- goods_master に storage_method（保存方法）を追加した（商品カードの裏面に表示する）
- 商品登録依頼は写真だけにした。goods_registration_request から request_type・goods_name・barcode・unit_weight・correction_reason を削除し、request_status を「0=依頼中 1=登録済み 2=登録しない」にした
- 先生のレビュー（11/16）：listing に recommend（おすすめフラグ）を追加した。営業時間の CHECK を削除し、日付をまたぐ営業を許した
- 11/16：店舗側のキャンセルだけ戻した。orders に cancel_date を戻し、order_status に「3=キャンセル（店舗都合）」を足した（DECISIONS D-14・D-28）
- 11/18（先生のレビュー）：orders から pickup_code（受け取りコード）を削除した。受け取り確認コードは「店舗ID-注文ID」（例：1-100）を画面で組み立てて表示し、店舗が入力したコードで照合する（DECISIONS D-24・D-25）
- 11/20：account に avatar_image（プロフィール画像）を追加した。
- 11/26：討論の結果、受け取り締め切り日時を表す列であることを明確にするため、注文テーブルの列名を pickup_schedule から pickup_deadline に変更する。データ型は datetime とし、締め切り日時の算出には従来のルールを適用する。

## ダミーデータ（JSON）を作るときのメモ

- 列名・型は、この版のテーブル定義書に合わせてください
- 日時（datetime）は `2026-11-13 18:00:00` の形、時刻（time）は `18:00:00` の形
- bit の列（category_status、goods_status、goods_changed、listing_status、recommend）は 0 / 1
- 営業時間は日付をまたいでもよい（例：opening_time `10:00:00`、closing_time `02:00:00`）
- 状態を表す int の列の意味
  - account.account_status：0=利用可 1=停止 2=退会
  - store.store_status：0=利用可 1=停止 2=退店
  - orders.order_status：0=受け取り待ち 1=受け取り済み 2=期限切れ（2に変える処理はMVPでは作らない。DECISIONS D-23） 3=キャンセル（店舗都合。cancel_date も入れる。理由の列はない）
  - goods_registration_request.request_status：0=依頼中 1=登録済み 2=登録しない
- 商品名・アレルゲン・保存方法は goods_master、価格・残数・食品期限・締め切りは listing、店舗名・営業時間は store から取ります（商品カードは listing を中心に goods_master と store をつなぎます）
- store.store_genre・store_image は、店舗情報の画面がMVP対象外なので、ダミーデータで入れてください
- 店舗カードには距離を出さず、住所（store.address）を表示します
- orders.pickup_deadline は受け取りの締め切り日時です（閉店時間と、注文内でいちばん早い sale_end の早いほう）。ユーザーは時刻を選びません（DECISIONS D-05）

## 未決のこと

- 今のところありません（P-07 は D-23 として決定）
