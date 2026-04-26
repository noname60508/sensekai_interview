<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\books;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テストデータをセットアップ - データベースを毎回テスト後にリセット
     * 每次測試後重置資料庫 - 設置測試數據
     */
    protected function setUp(): void
    {
        parent::setUp();
        books::create([
            'title' => 'Test Book 1',
            'author' => 'Author One',
            'isbn10' => '4802615116',
            'isbn13' => '9784802615112',
            'publisher' => 'Publisher A',
            'publication_date' => '2024-01-15',
        ]);

        books::create([
            'title' => 'Test Book 2',
            'author' => 'Author Two',
            'isbn10' => '4802615116',
            'isbn13' => '9784802615112',
            'publisher' => 'Publisher B',
            'publication_date' => '2024-02-20',
        ]);
    }

    /**
     * 測試取得書籍列表 - 成功
     * 書籍一覧取得テスト - 成功
     */
    public function test_index_returns_paginated_books_list()
    {
        $response = $this->getJson('/api/v1/books?page=1&pageSize=10');
        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
    }

    /**
     * 書籍一覧取得テスト - 必須パラメータなし
     * 測試取得書籍列表 - 缺少必要參數
     */
    public function test_index_fails_without_page_parameter()
    {
        $response = $this->getJson('/api/v1/books?pageSize=10');

        $response->assertStatus(400);
    }

    /**
     * 書籍一覧取得テスト - 無効なページパラメータ
     * 測試取得書籍列表 - page 不是整數
     */
    public function test_index_fails_with_invalid_page_parameter()
    {
        $response = $this->getJson('/api/v1/books?page=abc&pageSize=10');

        $response->assertStatus(400);
    }

    /**
     * 書籍検索テスト - タイトル検索
     * 測試搜尋書籍 - 按標題搜尋
     */
    public function test_search_by_title()
    {
        $response = $this->getJson('/api/v1/books?title=Test Book 1&page=1');
        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
    }

    /**
     * 書籍検索テスト - 著者検索
     * 測試搜尋書籍 - 按作者搜尋
     */
    public function test_search_by_author()
    {
        $response = $this->getJson('/api/v1/books?author=Author One&page=1');
        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
    }

    /**
     * 書籍検索テスト - ISBN10検索
     * 測試搜尋書籍 - 按 ISBN10 搜尋
     */
    public function test_search_by_isbn10()
    {
        $response = $this->getJson('/api/v1/books?isbn10=4802615116&page=1');

        $response->assertStatus(200);
    }

    /**
     * 書籍検索テスト - ページパラメータなし
     * 測試搜尋書籍 - 缺少 page 參數
     */
    public function test_search_fails_without_page_parameter()
    {
        $response = $this->getJson('/api/v1/books?title=Test Book');

        $response->assertStatus(400);
    }

    /**
     * 新規書籍作成テスト - 成功
     * 測試建立新書籍 - 成功
     */
    public function test_store_creates_book_successfully()
    {
        $bookData = [
            'title' => 'New Book',
            'author' => 'New Author',
            'isbn10' => '4802615116',
            'isbn13' => '9784802615112',
            'publisher' => 'New Publisher',
            'publicationDate' => '2024-03-10',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);
        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
            'author' => 'New Author',
        ]);
    }

    /**
     * 新規書籍作成テスト - 必須フィールドなし
     * 測試建立新書籍 - 缺少必要欄位
     */
    public function test_store_fails_without_required_fields()
    {
        $bookData = ['title' => 'New Book'];
        $response = $this->postJson('/api/v1/books', $bookData);
        $response->assertStatus(400);
    }

    /**
     * 新規書籍作成テスト - タイトル最大長超過
     * 測試建立新書籍 - 標題超過最大長度
     */
    public function test_store_fails_with_title_exceeding_max_length()
    {
        $bookData = [
            'title' => str_repeat('A', 51), // 50文字を超える / 超過 50 字符
            'author' => 'Author',
            'isbn10' => '4802615116',
            'isbn13' => '9784802615112',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(400);
    }

    /**
     * 新規書籍作成テスト - ISBN10形式エラー
     * 測試建立新書籍 - ISBN10格式錯誤
     */
    public function test_store_fails_with_isbn10_invalid_format()
    {
        $bookData = [
            'title' => 'New Book',
            'author' => 'Author',
            'isbn10' => str_repeat('A', 11), // 10文字を超える / 超過 10 字符
            'isbn13' => '9784802615112',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(400);
    }

    /**
     * 新規書籍作成テスト - ISBN13最大長超過
     * 測試建立新書籍 - ISBN13長度錯誤
     */
    public function test_store_fails_with_isbn13_exceeding_max_length()
    {
        $bookData = [
            'title' => 'New Book',
            'author' => 'Author',
            'isbn10' => '4802615116',
            'isbn13' => str_repeat('1', 14), // 13文字を超える / 超過 13 字符
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(400);
    }

    /**
     * 新規書籍作成テスト - 無効な日付形式
     * 測試建立新書籍 - 無效的日期格式
     */
    public function test_store_fails_with_invalid_date_format()
    {
        $bookData = [
            'title' => 'New Book',
            'author' => 'Author',
            'isbn10' => '4802615116',
            'isbn13' => '9784802615112',
            'publicationDate' => 'invalid-date',
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(400);
    }

    /**
     * 単一書籍取得テスト - 成功
     * 測試取得單本書籍 - 成功
     */
    public function test_show_returns_single_book()
    {
        $book = books::first();
        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);
        $this->assertEquals($book->id, $response->json('data.id'));
    }

    /**
     * 単一書籍取得テスト - 書籍が存在しない
     * 測試取得單本書籍 - 書籍不存在
     */
    public function test_show_fails_with_nonexistent_book()
    {
        $response = $this->getJson('/api/v1/books/0');

        $response->assertStatus(404);
    }

    /**
     * 書籍更新テスト - 全フィールド更新成功
     * 測試更新書籍 - 成功更新所有欄位
     */
    public function test_update_modifies_book_successfully()
    {
        $book = books::first();

        $updateData = [
            'title' => 'Updated Book Title',
            'author' => 'Updated Author',
            'isbn10' => '4798075272',
            'isbn13' => '9784798075273',
            'publisher' => 'Updated Publisher',
            'publicationDate' => '2024-06-15',
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(204);

        // 驗證數據已在資料庫中更新
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Book Title',
            'author' => 'Updated Author',
            'isbn10' => '4798075272',
            'isbn13' => '9784798075273',
        ]);
    }

    /**
     * 書籍更新テスト - 部分フィールド更新
     * 測試更新書籍 - 部分更新
     */
    public function test_update_modifies_partial_fields()
    {
        $book = books::first();
        $originalTitle = $book->title;

        $updateData = [
            'author' => 'Updated Author Only',
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(204);

        // 驗證只有指定的欄位被更新
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => $originalTitle, // 標題未變
            'author' => 'Updated Author Only',
        ]);
    }

    /**
     * 書籍更新テスト - 無効な日付形式
     * 測試更新書籍 - 無效的日期格式
     */
    public function test_update_fails_with_invalid_date()
    {
        $book = books::first();

        $updateData = [
            'publicationDate' => 'invalid-date',
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(400);
    }

    /**
     * 書籍更新テスト - isbn10格式錯誤
     * 測試更新書籍 - isbn10格式錯誤
     */
    public function test_update_fails_with_invalid_isbn10()
    {
        $book = books::first();

        $updateData = [
            'isbn10' => str_repeat('A', 10),
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(400);
    }

    /**
     * 書籍更新テスト - isbn13最大長超過
     * 測試更新書籍 - isbn13長度錯誤
     */
    public function test_update_fails_with_invalid_isbn13()
    {
        $book = books::first();

        $updateData = [
            'isbn13' => str_repeat('1', 14), // 13文字を超える / 超過 13 字符
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(400);
    }

    /**
     * 書籍更新テスト - タイトル最大長超過
     * 測試更新書籍 - 標題超過最大長度
     */
    public function test_update_fails_with_title_exceeding_max_length()
    {
        $book = books::first();

        $updateData = [
            'title' => str_repeat('B', 51),
        ];

        $response = $this->patchJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(400);
    }

    /**
     * 書籍更新テスト - 書籍が存在しない
     * 測試更新書籍 - 書籍不存在
     */
    public function test_update_fails_with_nonexistent_book()
    {
        $updateData = [
            'title' => 'Updated Title',
        ];

        $response = $this->patchJson('/api/v1/books/0', $updateData);

        $response->assertStatus(404);
    }

    /**
     * 書籍削除テスト - 成功
     * 測試刪除書籍 - 成功
     */
    public function test_destroy_deletes_book_successfully()
    {
        $book = books::first();
        $bookId = $book->id;

        $response = $this->deleteJson("/api/v1/books/{$bookId}");

        $response->assertStatus(204);

        // 書籍がソフトデリートされていることを確認 / 驗證書籍已軟刪除（使用軟刪除）
        $this->assertSoftDeleted('books', ['id' => $bookId]);
    }

    /**
     * 書籍削除テスト - 書籍が存在しない
     * 測試刪除書籍 - 書籍不存在
     */
    public function test_destroy_fails_with_nonexistent_book()
    {
        $response = $this->deleteJson('/api/v1/books/0');

        $response->assertStatus(404);
    }

    /**
     * 書籍削除テスト - 削除後の書籍取得失敗（ソフトデリート検証）
     * 測試刪除書籍後無法查詢（軟刪除驗證）
     */
    public function test_show_fails_after_soft_delete()
    {
        $book = books::first();
        $bookId = $book->id;

        $this->deleteJson("/api/v1/books/{$bookId}");

        $response = $this->getJson("/api/v1/books/{$bookId}");

        $response->assertStatus(404);
    }
}
