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

5. **マイグレーション実行**
   ```bash
   php artisan migrate
   ```

完了です！
