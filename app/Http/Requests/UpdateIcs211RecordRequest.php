<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIcs211RecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'nullable|integer|min:1|max:5',
            'order_request_number' => 'sometimes|required|string|max:255',
            'checkin_location' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'remarks' => 'nullable|string',
            'remarks_image_attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'nullable|string|in:pending,ongoing,completed',
            
            // Check-in details array validation
            'check_in_details' => 'nullable|array',
            'check_in_details.*.uuid' => 'nullable|exists:check_in_details,uuid',
            'check_in_details.*.personnel_id' => 'nullable|exists:personnels,id',
            'check_in_details.*.order_request_number' => 'required|string|max:255',
            'check_in_details.*.checkin_date' => 'required|date_format:Y-m-d',
            'check_in_details.*.checkin_time' => 'required|date_format:H:i',
            'check_in_details.*.kind' => 'required|string|max:255',
            'check_in_details.*.category' => 'required|string|in:Personnel,Equipment',
            'check_in_details.*.type' => 'required|string|max:255',
            'check_in_details.*.resource_identifier' => 'required|string|max:255',
            'check_in_details.*.name_of_leader' => 'required|string|max:255',
            'check_in_details.*.contact_information' => 'required|string|max:255',
            'check_in_details.*.quantity' => 'required|integer|min:1',
            'check_in_details.*.department' => 'required|string|max:255',
            'check_in_details.*.departure_point_of_origin' => 'required|string|max:255',
            'check_in_details.*.departure_date' => 'required|date_format:Y-m-d',
            'check_in_details.*.departure_time' => 'required|date_format:H:i',
            'check_in_details.*.departure_method_of_travel' => 'required|string|max:255',
            'check_in_details.*.with_manifest' => 'nullable|boolean',
            'check_in_details.*.incident_assignment' => 'nullable|string|max:255',
            'check_in_details.*.other_qualifications' => 'nullable|string',
            'check_in_details.*.sent_resl' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.integer' => 'Type must be a number',
            'type.min' => 'Type must be between 1 and 5',
            'type.max' => 'Type must be between 1 and 5',
            'status.in' => 'Status must be pending, ongoing, or completed',
            'name.required' => 'Operation name is required',
            'order_request_number.required' => 'Order request number is required',
            'checkin_location.required' => 'Check-in location is required',
            
            'check_in_details.*.uuid.exists' => 'Check-in detail not found',
            'check_in_details.*.personnel_id.exists' => 'Personnel not found in check-in detail',
            'check_in_details.*.order_request_number.required' => 'Order request number is required in check-in detail',
            'check_in_details.*.checkin_date.required' => 'Check-in date is required in check-in detail',
            'check_in_details.*.checkin_date.date_format' => 'Check-in date must be in YYYY-MM-DD format',
            'check_in_details.*.checkin_time.required' => 'Check-in time is required in check-in detail',
            'check_in_details.*.checkin_time.date_format' => 'Check-in time must be in HH:MM format (24-hour)',
            'check_in_details.*.kind.required' => 'Kind is required in check-in detail',
            'check_in_details.*.category.required' => 'Category is required in check-in detail',
            'check_in_details.*.category.in' => 'Category must be either Personnel or Equipment',
            'check_in_details.*.type.required' => 'Type is required in check-in detail',
            'check_in_details.*.resource_identifier.required' => 'Resource identifier is required in check-in detail',
            'check_in_details.*.name_of_leader.required' => 'Name of leader is required in check-in detail',
            'check_in_details.*.contact_information.required' => 'Contact information is required in check-in detail',
            'check_in_details.*.quantity.required' => 'Quantity is required in check-in detail',
            'check_in_details.*.quantity.integer' => 'Quantity must be a number',
            'check_in_details.*.quantity.min' => 'Quantity must be at least 1',
            'check_in_details.*.department.required' => 'Department is required in check-in detail',
            'check_in_details.*.departure_point_of_origin.required' => 'Departure point is required in check-in detail',
            'check_in_details.*.departure_date.required' => 'Departure date is required in check-in detail',
            'check_in_details.*.departure_date.date_format' => 'Departure date must be in YYYY-MM-DD format',
            'check_in_details.*.departure_time.required' => 'Departure time is required in check-in detail',
            'check_in_details.*.departure_time.date_format' => 'Departure time must be in HH:MM format (24-hour)',
            'check_in_details.*.departure_method_of_travel.required' => 'Method of travel is required in check-in detail',
        ];
    }
}
