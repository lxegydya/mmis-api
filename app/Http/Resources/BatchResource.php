<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_name' => $this->batch_name,
            'batch_start' => $this->batch_start,
            'batch_end' => $this->batch_end,
            'batch_status' => $this->batch_status,
            'created_at' => date_format($this->created_at, 'd-M-Y H:i:s')
        ];
    }
}
