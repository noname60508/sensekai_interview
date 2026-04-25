<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\booksService;

class booksController extends Controller
{

    protected $booksService;
    public function __construct(booksService $booksService)
    {
        $this->booksService = $booksService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'           => ['nullable', 'string', 'max:50'],
            'author'          => ['nullable', 'string'],
            'isbn'            => ['nullable', 'string'],
            'publisher'       => ['nullable', 'string'],
            'publicationDate' => ['nullable', 'date'],
            'page'            => ['required', 'integer', 'min:1'],
            'pageSize'        => ['nullable', 'integer', 'min:1'],
        ], [
            'title'           => '【title:書籍名】は文字列で最大50文字までです',
            'author'          => '【author:著者名】は文字列です',
            'isbn'            => '【isbn:ISBN】は文字列です',
            'publisher'       => '【publisher:出版社】は文字列です',
            'publicationDate' => '【publicationDate:出版日】は日付形式です',
            'page'            => '【page:頁碼】は必須で、整数で、最小値は1です',
            'pageSize'        => '【pageSize:ページサイズ】は整数で、最小値は1です',
        ]);
        // エラー回傳
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        try {
            $pageSize = $request->input('pageSize', $this->defaultPageSize);
            $booksList = $this->booksService->getBooksList($request, $pageSize);

            return response($booksList, 200);
        } catch (\Throwable $e) {
            return response(env('APP_DEBUG') ? $e->getMessage() : null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'           => ['required', 'string', 'max:50'],
            'author'          => ['required', 'string'],
            'isbn'            => ['required', 'string'],
            'publisher'       => ['nullable', 'string'],
            'publicationDate' => ['nullable', 'date'],
        ], [
            'title'           => '【title:書籍名】は必須で、文字列で最大50文字までです',
            'author'          => '【author:著者名】は必須で、文字列です',
            'isbn'            => '【isbn:ISBN】は必須で、文字列です',
            'publisher'       => '【publisher:出版社】は文字列です',
            'publicationDate' => '【publicationDate:出版日】は日付形式です',
        ]);
        // エラー回傳
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        try {
            $this->booksService->createBook($request->all());

            return response('Created', 201);
        } catch (\Throwable $e) {
            return response(env('APP_DEBUG') ? $e->getMessage() : null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $book = $this->booksService->getBookById($id);
            return response($book, 200);
        } catch (\Throwable $e) {
            return response(env('APP_DEBUG') ? $e->getMessage() : null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title'           => ['nullable', 'string', 'max:50'],
            'author'          => ['nullable', 'string'],
            'isbn'            => ['nullable', 'string'],
            'publisher'       => ['nullable', 'string'],
            'publicationDate' => ['nullable', 'date'],
        ], [
            'title'           => '【title:書籍名】は文字列で最大50文字までです',
            'author'          => '【author:著者名】は文字列です',
            'isbn'            => '【isbn:ISBN】は文字列です',
            'publisher'       => '【publisher:出版社】は文字列です',
            'publicationDate' => '【publicationDate:出版日】は日付形式です',
        ]);
        // エラー回傳
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        try {
            $this->booksService->updateBook($id, $request);

            return response('Updated', 204);
        } catch (\Throwable $e) {
            return response(env('APP_DEBUG') ? $e->getMessage() : null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->booksService->deleteBook($id);

            return response('Deleted', 204);
        } catch (\Throwable $e) {
            return response(env('APP_DEBUG') ? $e->getMessage() : null, 500);
        }
    }
}
