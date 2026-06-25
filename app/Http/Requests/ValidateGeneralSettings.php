<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ValidateGeneralSettings extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // HTML checkboxes submit "on" when ticked and nothing when unticked;
        // normalise both toggles to 0/1 before validation.
        $this->request->add([
            'membership' => $this->request->get('membership') === 'on' ? '1' : '0',
            'email_verification' => $this->request->get('email_verification') === 'on' ? '1' : '0',
        ]);

        return [
            'website_name' => 'required|string',
            'tagline' => 'required|string',
            'posts_per_page' => 'required|integer',
            'comments_per_page' => 'required|integer',
            'contact_email' => 'required|email',
            'membership' => 'required|in:0,1',
            'email_verification' => 'required|in:0,1',
            'active_template_name' => 'nullable|string',
        ];
    }
}
