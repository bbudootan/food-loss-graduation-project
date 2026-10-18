# Wireframes

本ディレクトリでは、JEC Food Rescue のワイヤーフレームを **アクター → 画面 → バージョン** の順で管理します。

- [01_user：一般利用者向け](./01_user/README.md)
- [02_store_operator：店舗運営者向け](./02_store_operator/README.md)
- [03_system_admin：システム管理者向け](./03_system_admin/README.md)

## 管理ルール

- **v1 / v2 / v3 は元資料の番号を保持**し、作成していなかった版は追加しません。
- 各画面のバージョンは独立して増やします（例：Homepage は v3、退会ページは v3 のみ）。
- `00_common` には一般利用者向けの Global Navigation と Account Drawer を配置します。
- 商品一覧と店舗一覧の **一覧表示・地図表示は同一ページ内の状態** として管理します。
- 店舗運営者・システム管理者の UI は今後設計し、画面ファイルができた時点でフォルダを追加します。
- Git の Commit 履歴はファイルの版管理とは別に、日々の細かな変更記録として使用します。

## 現在の設計方針

一般利用者向けは閲覧領域を広く保つ右側の Account Drawer、管理者・店舗運営者向けは頻繁な切り替えを重視した固定 Sidebar を想定します。
