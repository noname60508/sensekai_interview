# Sensekai Interview

## 使用技術およびそのバージョン

- **Laravel**: 12
- **PHP**: 8.4

## 環境構成
- **windows**: 11
- **MySQL**: 8

## 実行手順

1. **Composer 依存関係をインストール**
   ```bash
   composer install
   ```

2. **データベース作成**
   - `sensekai_interview` という名前のデータベースを作成します

3. **環境ファイル設定**
   - `.env` ファイルをコピーします
   ```bash
   cp .env.example .env
   ```

4. **環境変数を設定**
   ```env
   DB_HOST=your_ip
   DB_PORT=your_port
   DB_DATABASE=sensekai_interview
   DB_USERNAME=username
   DB_PASSWORD=password
   ```

5. **Application Key を生成**
   ```bash
   php artisan key:generate
   ```

6. **マイグレーション実行**
   ```bash
   php artisan migrate
   ```

7. **開発サーバーを起動**
   ```bash
   php artisan serve
   ```
   - サーバーは `http://127.0.0.1:8000` で起動します
   - API エンドポイントは `http://127.0.0.1:8000/api/v1` でアクセス可能です

8. **テスト実行（オプション）**
   ```bash
   php artisan test
   ```

完了です！
