<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\{User};
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use RestCord\DiscordClient;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Discord authentication page.
     *
     * @return Response
     */
    public function redirectToDiscord() {
        return Socialite::driver('discord')
            // Don't require Discord to send back and email
            // https://discord.com/developers/docs/topics/oauth2#shared-resources-oauth2-scopes
            ->setScopes(['identify', 'guilds']) // If changing these scopes, update the call to refresh a user's access (search 'refresh_token')
            // Don't prompt the user to accept our app's usage of their Discord profile EVERY time (only on first signup)
            // https://discord.com/developers/docs/topics/oauth2#authorization-code-grant-authorization-url-example
            ->with(['prompt' => 'none'])
            ->redirect();
    }

    /**
     * Obtain the user information from Discord.
     *
     * @return Response
     */
    public function handleDiscordCallback()
    {
        try {
            $unauthUser = Socialite::driver('discord')->user();
        } catch (Exception $e) {
            return redirect('auth/discord');
        }

        $id = $unauthUser->getId();

        if (!$id) {
            abort(403, "Didn't receive your ID from Discord. Try again.");
        }

        $discord = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]);

        $authUser = $this->findUser($unauthUser, 'discord');

        if ($authUser) {
            if ($authUser->banned_at) {
                abort(403, 'You have been banned.');
            }
            Auth::login($authUser, true);

            Auth::user()->update([
                'discord_token'         => $unauthUser->token,
                'discord_refresh_token' => $unauthUser->refreshToken,
                'discord_token_expiry'  => date('Y-m-d H:i:s', time() + $unauthUser->expiresIn),
            ]);

            return redirect()->route('home');
        } else if ($unauthUser) {
            $user = User::create([
                'username'              => $unauthUser->getName(),
                // 'email'                 => $unauthUser->getEmail(),
                'discord_username'      => $unauthUser->getNickname(),
                'discord_id'            => $id,
                'discord_avatar'        => $unauthUser->getAvatar(),
                'discord_token'         => $unauthUser->token,
                'discord_refresh_token' => $unauthUser->refreshToken,
                'discord_token_expiry'  => date('Y-m-d H:i:s', time() + $unauthUser->expiresIn),
                'password'              => null,
            ]);

            Auth::login($user, true);

            return redirect()->route('home');
        } else {
            abort(403, "Something went wrong with the data Discord sent us. Try again.");
        }
    }

    /**
     * Used when a user attempts to log in via a social service
     *
     * @param $user    Laravel\Socialite\AbstractUser
     * @param $service string The name of the service we're connecting to. eg. 'google' or 'facebook'
     *
     * @return App\User
     */
    private function findUser($user, $service) {
        $serviceField = $service . '_id';
        $authUser = User::where($serviceField, $user->id)->first();
        return $authUser;
    }
}
