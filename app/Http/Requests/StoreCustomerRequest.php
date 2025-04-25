<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Brick\PhoneNumber\PhoneNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class StoreCustomerRequest extends FormRequest {
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array {
        $eighteenYearsAgo = now()->subYears(18)->format('Y-m-d');
        return [
            'name' => 'required|string|max:255',
            'email' => [
            'required',
            'email',
            function($attribute, $value, $fail) {
                $exists = DB::table('customers')
                        ->whereRaw('LOWER(email) = ?', [strtolower($value)])
                        ->exists();
                
                if ($exists) {
                    $fail('The email is already being used.');
                }
            }
        ],
            'phone' => ['required', 'phone:GB'],
            'dob' => 'required|date|before:' . $eighteenYearsAgo
        ];

    }

    public function messages(): array {
        return [
            'name.required' => 'Please enter a name.',
            'email.required' => 'Please enter an email.',
            'email.unique' => 'Email is already being used.',
            'phone.required' => 'Please enter a valid phone number.',
            'phone.phone' => 'Phone number is not valid.',
            'dob.required' => 'Please enter a date of birth.',
            'dob.before' => 'Must be over 18 to be a customer.'
        ];
    }

    protected function prepareForValidation(): void {
        if ($this->has('phone') &&  !empty($this->phone)) {
            $this->merge([
                'phone' => PhoneNumber::parse($this->phone, 'GB')->__toString()
            ]);
        }
    }
    
}
