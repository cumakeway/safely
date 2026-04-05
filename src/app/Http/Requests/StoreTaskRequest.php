<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
         return $this->user()?->isManager() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string', 'max:2000'],
            'due_date'         => ['required', 'date', 'after_or_equal:today'],
            'assigned_user_id' => ['required', 'exists:users,id'],
            'priority'         => ['required', 'in:low,medium,high'],
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'assigned_user_id.exists' => 'The selected user does not exist.',
            'description.required'    => 'The description is required'
        ];
    }
}
