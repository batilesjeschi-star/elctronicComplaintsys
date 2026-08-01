<?php

namespace App\Http\Requests;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintStatusRequest extends FormRequest
{
    /**
     * The 'admin' route middleware already guards this route, but we
     * double-check here as well since Form Requests run before controllers.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Complaint::STATUSES))],
            'admin_remarks' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'resolution_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resolution_photo.image' => 'The resolution photo must be an image file.',
            'resolution_photo.mimes' => 'The resolution photo must be a JPEG or PNG file.',
            'resolution_photo.max' => 'The resolution photo may not be larger than 5MB.',
        ];
    }
}
