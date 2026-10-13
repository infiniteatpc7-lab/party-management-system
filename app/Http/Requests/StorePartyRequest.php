<?php

namespace App\Http\Requests;

use App\Services\CoordinateExtractor;
use Illuminate\Foundation\Http\FormRequest;

class StorePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_lead' => $this->boolean('is_lead'),
        ]);

        // If latitude or longitude are missing, attempt extraction from map_link
        if ((empty($this->latitude) || empty($this->longitude)) && ! empty($this->map_link)) {
            $extracted = CoordinateExtractor::extract($this->map_link);
            if ($extracted) {
                $this->merge([
                    'latitude' => $extracted['latitude'],
                    'longitude' => $extracted['longitude'],
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'party_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:25'],
            'map_link' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'party_type' => ['required', 'in:new,old'],
            'priority_status' => ['required', 'in:priority,non_priority'],
            'is_lead' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'party_name.required' => 'Party Name is required.',
            'latitude.required' => 'Latitude is required. Enter coordinates or provide a Google Maps link containing location data.',
            'longitude.required' => 'Longitude is required. Enter coordinates or provide a Google Maps link containing location data.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'party_type.in' => 'Party type must be either New or Old.',
            'priority_status.in' => 'Priority status must be Priority or Non-Priority.',
        ];
    }
}
