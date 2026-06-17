<?php
/**
 * Laravella CMS
 * File: PageRepository.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 24.10.2019
 */

namespace App\Repositories;

use App\Http\Models\User;
use Illuminate\Support\Facades\Auth;
use Image;
use Hash;

class UserRepository extends BaseRepository
{
    private  $logged_user_id;

    protected $select_fields = [
        'email',
        'username',
        'name',
        'surname',
        'gender',
        'country',
        'city',
        'role_id',
        'facebook_url',
        'twitter_url',
        'google_url',
        'instagram_url',
        'linkedin_url',
        'xing_url',
        'about_me',
        'created_at',
        'avatar',
    ];

    public function __construct(User $model)
    {
        parent::__construct();
        $this->model = $model;
    }


    private function get_logged_user_id()
    {
        if(!is_logged_in()) return false;

        $this->logged_user_id = get_logged_user_id();
    }

    /**
     * Update the user's own profile from validated input.
     *
     * Only whitelisted (validated) fields are applied, and privileged columns
     * (role_id, provider, provider_id) are stripped so a front-end user can
     * never escalate their role or hijack a social identity through this path.
     *
     * @param  int  $id
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return bool
     */
    public function update(int $id, $request)
    {
        $data = $request->validated();

        unset($data['role_id'], $data['provider'], $data['provider_id']);

        // The validated avatar (when present) is an uploaded file; replace it
        // with the stored image path before persisting.
        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage($request->file('avatar'));
        } else {
            unset($data['avatar']);
        }

        $user = $this->model->findOrFail($id);

        return (bool) $user->update($data);
    }


    public function changePassword($request)
    {
        if (!is_logged_in() || !(Hash::check($request->current_password, \Auth::user()->password))) return false;

        $this->get_logged_user_id();

        $user = $this->model->findOrFail($this->logged_user_id);
        $result = $user->update(['password'=> $request->password]);


        return $result;
    }
    
}