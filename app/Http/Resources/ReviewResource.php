<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'reviewId' => $this->review_id,
            'reviewerName' => $this->reviewer_name,
            'rating' => $this->rating,
            'reviewDate' => $this->review_date->format('Y-m-d'),
            'comment' => $this->comment,
        ];
    }
}
