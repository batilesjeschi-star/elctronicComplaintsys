<?php

namespace App\Http\Requests;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    /**
     * Any authenticated resident is allowed to file a complaint.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Complaint::CATEGORIES))],
            'location' => ['required', 'string', 'max:255'],

            // GPS coordinates are optional - residents may not always share their location.
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // Up to 5 photos, JPEG or PNG, 5MB max each.
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.max' => 'You may upload a maximum of 5 photos.',
            'images.*.image' => 'Each uploaded file must be an image.',
            'images.*.mimes' => 'Photos must be JPEG or PNG files.',
            'images.*.max' => 'Each photo may not be larger than 5MB.',
        ];
    }
}
