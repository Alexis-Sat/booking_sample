<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'room_id' => 'sometimes|required|exists:meeting_rooms,id',
            'start_time' => 'sometimes|required|date|after_or_equal:now',
            'end_time' => 'sometimes|required|date|after:start_time',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => trans('validation.room_id.required'),
            'room_id.exists' =>  trans('validation.room_id.exists'),
            'start_time.required' => trans('validation.start_time.required'),
            'start_time.date' => trans('validation.start_time.date'),
            'start_time.after_or_equal' => trans('validation.start_time.after_or_equal'),
            'end_time.required' => trans('validation.end_time.required'),
            'end_time.date' => trans('validation.end_time.dates'),
            'end_time.after' => trans('validation.end_time.after'),
            'title.required' => trans('validation.title.required'),
            'title.max' => trans('validation.title.max'),
            'description.string' => trans('validation.description.string'),
        ];
    }
}
