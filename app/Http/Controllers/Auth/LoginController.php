<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Socialite;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $authUser = $this->findOrLinkUser($socialUser, $provider);

        if ($authUser) {
            Auth::login($authUser, true);
            return redirect($this->redirectTo);
        }

        $validator = $this->validateSocialUser($socialUser);

        if (gettype($validator) === "object") {
            return redirect('login')
                ->withErrors($validator)
                ->withInput();
        }

        $registered_user = $this->createUser($socialUser, $provider);
        Auth::login($registered_user, true);

        return redirect($this->redirectTo);
    }

    /**
     * Resolve an existing account for the social user. Match first on the
     * provider id, then fall back to linking by the provider-supplied email
     * (so a user who originally registered with that email is not duplicated).
     * The lookup + linking runs in a transaction to avoid races.
     */
    private function findOrLinkUser($socialUser, string $provider): ?User
    {
        return DB::transaction(function () use ($socialUser, $provider) {
            $authUser = User::where('provider_id', $socialUser->id)
                ->where('provider', $provider)
                ->first();

            if ($authUser) {
                return $authUser;
            }

            if (empty($socialUser->email)) {
                return null;
            }

            $existing = User::where('email', $socialUser->email)->first();

            if ($existing) {
                // Link the social identity to the existing account. Provider
                // fields are set explicitly (they are not mass assignable).
                $existing->provider = $provider;
                $existing->provider_id = $socialUser->id;
                $existing->save();

                return $existing;
            }

            return null;
        });
    }

    /**
     * Create a brand new user from the social profile. Provider identity
     * fields are assigned explicitly rather than mass assigned, and privileged
     * fields (role_id) are left to their database default.
     *
     * @param  object  $user      Socialite user object
     * @param  string  $provider  Social auth provider key
     */
    public function createUser($user, string $provider): User
    {
        return DB::transaction(function () use ($user, $provider) {
            $newUser = new User([
                'name'     => $user->name,
                'email'    => $user->email,
                'username' => $this->get_user_name($user->email),
            ]);

            $newUser->provider = $provider;
            $newUser->provider_id = $user->id;
            $newUser->save();

            return $newUser;
        });
    }

    private function validateSocialUser($data)
    {
        $username = $this->get_user_name($data->email);

        $array_to_validate = [
            "email"    => $data->email,
            "name"     => $data->name,
            "username" => $username
        ];

        $validator = Validator::make($array_to_validate, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        return true;
    }

    private function get_user_name(string $email):string
    {
        $position = strpos( $email,"@");
        $username =  substr($email,0, $position);

        return $username;
    }

    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'username';
        return [
            $field     => $request->get($this->username()),
            'password' => $request->password,
        ];
    }
}
