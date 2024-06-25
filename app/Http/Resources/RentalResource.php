<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
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
            'id' => $this->id,
            'rental_space_name' => $this->rental_space_name,
            'rental_code' => $this->rental_code,
            'owner_name' => $this->RentalSpaceOwner->owner_name,
            'owner_phone_number' => $this->RentalSpaceOwner->owner_phone_number,
            'name' => $this->RentalSpaceCity->name,
            'thana_name' => $this->RentalSpaceThanaPoZip->thana_name,
            'po_name' => $this->RentalSpaceThanaPoZip->po_name,
            'road_no' => $this->road_no,
            'house_no' => $this->house_no,
        ];
    }
}
