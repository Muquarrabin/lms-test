<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommonFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'file_name'     => $this->file_name,
            'mime_type'     => $this->mime_type,
            'size'          => $this->size,
            'file_url'      => $this->getUrl(),
            'uuid'          => $this->uuid,
        ];
    }
}
