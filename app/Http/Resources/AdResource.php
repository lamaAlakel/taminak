<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'image'      => $this->image,
            'description'   =>$this->description ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
