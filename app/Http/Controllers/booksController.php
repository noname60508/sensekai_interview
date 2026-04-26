<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Services\booksService;
use App\Http\Resources\BookResource;

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
            'isbn10'          => ['nullable', 'string'],
            'isbn13'          => ['nullable', 'string'],
            'publisher'       => ['nullable', 'string'],
            'publicationDate' => ['nullable', 'date'],
            'page'            => ['required', 'integer', 'min:1'],
            'pageSize'        => ['nullable', 'integer', 'min:1'],
        ], [
            'title'           => '【title:書籍名】は文字列で最大50文字までです',
            'author'          => '【author:著者名】は文字列です',
            'isbn10'          => '【isbn10:ISBN10】は文字列です',
            'isbn13'          => '【isbn13:ISBN13】は文字列です',
            'publisher'       => '【publisher:出版社】は文字列です',
            'publicationDate' => '【publicationDate:出版日】は日付形式です',
            'page'            => '【page:頁碼】は必須で、整数で、最小値は1です',
            'pageSize'        => '【pageSize:ページサイズ】は整数で、最小値は1です',
        ]);
        // エラー回傳
        if ($validator->fails()) {
            return response()->validationError($validator);
        }

        try {
            $pageSize = $request->input('pageSize', $this->defaultPageSize);
            $booksList = $this->booksService->getBooksList($request, $pageSize);
            $output = [
                'data' => BookResource::collection($booksList),
                'meta' => [
                    'currentPage' => $booksList->currentPage(),
                    'lastPage' => $booksList->lastPage(),
                    'perPage' => $booksList->perPage(),
                    'total' => $booksList->total(),
                ],
            ];

            return response()->apiResponse($output, 200);
        } catch (\Throwable $e) {
            return response()->apiFail($e);
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
            'isbn10'          => ['required', 'string'],
            'isbn13'          => ['required', 'string'],
            'publisher'       => ['nullable', 'string'],
            'publicationDate' => ['nullable', 'date'],
        ], [
            'title'           => '【title:書籍名】は必須で、文字列で最大50文字までです',
            'author'          => '【author:著者名】は必須で、文字列です',
            'isbn10'          => '【isbn10:ISBN10】は必須で、文字列です',
            'isbn13'          => '【isbn13:ISBN13】は必須で、文字列です',
            'publisher'       => '【publisher:出版社】は文字列です',
            'publicationDate' => '【publicationDate:出版日】は日付形式です',
        ]);
        // エラー回傳
        if ($validator->fails()) {
            return response()->validationError($validator);
        }

        try {
            if ($request->has('isbn10') && !$this->ISBN10Check($request->isbn10)) {
                return response()->apiResponse(['title' => ['【isbn10:ISBN10】は有効なISBN形式ではありません']], 400, 'Validation Error');
            }
            if ($request->has('isbn13') && !$this->ISBN13Check($request->isbn13)) {
                return response()->apiResponse(['title' => ['【isbn13:ISBN13】は有効なISBN形式ではありません']], 400, 'Validation Error');
            }
            $data = $this->booksService->createBook($request->all());

            return response()->apiResponse(new BookResource($data), 201);
        } catch (\Throwable $e) {
            return response()->apiFail($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->booksService->getBookById($id);
            return response()->apiResponse(new BookResource($data), 200);
        } catch (ModelNotFoundException $e) {
            return response()->apiFail($e, 404);
        } catch (\Throwable $e) {
            return response()->apiFail($e);
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
            'isbn10'          => ['nullable', 'string'],
            'isbn13'          => ['nullable', 'string'],
            'publisher'       => ['nullable', 'string'],
            'publicationDate' => ['nullable', 'date'],
        ], [
            'title'           => '【title:書籍名】は文字列で最大50文字までです',
            'author'          => '【author:著者名】は文字列です',
            'isbn10'          => '【isbn10:ISBN10】は文字列です',
            'isbn13'          => '【isbn13:ISBN13】は文字列です',
            'publisher'       => '【publisher:出版社】は文字列です',
            'publicationDate' => '【publicationDate:出版日】は日付形式です',
        ]);
        // エラー回傳
        if ($validator->fails()) {
            return response()->validationError($validator);
        }

        try {
            if ($request->has('isbn10') && !$this->ISBN10Check($request->isbn10)) {
                return response()->apiResponse(['title' => ['【isbn10:ISBN10】は有効なISBN形式ではありません']], 400, 'Validation Error');
            }
            if ($request->has('isbn13') && !$this->ISBN13Check($request->isbn13)) {
                return response()->apiResponse(['title' => ['【isbn13:ISBN13】は有効なISBN形式ではありません']], 400, 'Validation Error');
            }
            $this->booksService->updateBook($id, $request);

            return response()->apiResponse([], 204, 'Updated');
        } catch (ModelNotFoundException $e) {
            return response()->apiFail($e, 404);
        } catch (\Throwable $e) {
            return response()->apiFail($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->booksService->deleteBook($id);

            return response()->apiResponse([], 204, 'Deleted');
        } catch (ModelNotFoundException $e) {
            return response()->apiFail($e, 404);
        } catch (\Throwable $e) {
            return response()->apiFail($e);
        }
    }
}
