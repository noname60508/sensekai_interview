<?php

namespace App\Services;

use App\Models\books;

class booksService
{
    public function __construct()
    {
        //
    }

    public function getBooksList($request, $pageSize)
    {
        $search = [];
        // リクエストから検索条件を抽出 && 空の値は無視
        foreach ($request->only(['title', 'author', 'isbn', 'publisher', 'publicationDate']) as $key => $value) {
            if ($request->has($key) && !is_null($value) && $value !== '') {
                $search[$key] = $value;
            }
        }

        $query = books::select('id', 'title', 'author', 'isbn', 'publisher', 'publication_date')
            ->when(!empty($search['title']), function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search['title'] . '%');
            })
            ->when(!empty($search['author']), function ($query) use ($search) {
                $query->where('author', 'like', '%' . $search['author'] . '%');
            })
            ->when(!empty($search['isbn']), function ($query) use ($search) {
                $query->where('isbn', 'like', '%' . $search['isbn'] . '%');
            })
            ->when(!empty($search['publisher']), function ($query) use ($search) {
                $query->where('publisher', 'like', '%' . $search['publisher'] . '%');
            })
            ->when(!empty($search['publicationDate']), function ($query) use ($search) {
                $query->whereDate('publication_date', $search['publicationDate']);
            });

        return $query->paginate($pageSize);
    }

    public function createBook($data)
    {
        return books::create([
            'title'            => $data['title'],
            'author'           => $data['author'],
            'isbn'             => $data['isbn'],
            'publisher'        => $data['publisher'] ?? null,
            'publication_date' => $data['publicationDate'] ?? null,
        ]);
    }

    public function updateBook($id, $request)
    {
        $data = [];
        foreach ($request->only(['title', 'author', 'isbn', 'publisher', 'publicationDate']) as $key => $value) {
            // リクエストから更新条件を抽出
            if ($request->has($key)) {
                $key = $key === 'publicationDate' ? 'publication_date' : $key;
                $data[$key] = $value;
            }

            // 空の値はnullに変換 (ただし、title、author、isbnは必須項目なので空の値は無視)
            if ($value === '' && !in_array($key, ['title', 'author', 'isbn'])) {
                $data[$key] = null;
            }
        }

        $book = books::findOrFail($id);
        $book->update($data);
        return $book;
    }

    public function deleteBook($id)
    {
        $book = books::findOrFail($id);
        $book->delete();
    }

    public function getBookById($id)
    {
        return books::findOrFail($id);
    }
}
