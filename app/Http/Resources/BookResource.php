<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'author'          => $this->author,
            'isbn10'          => $this->isbn10,
            'isbn13'          => $this->isbn13,
            'publisher'       => $this->publisher ?? null,
            'publicationDate' => $this->publication_date ? Carbon::parse($this->publication_date)->toDateString() : null,
        ];
    }
}
