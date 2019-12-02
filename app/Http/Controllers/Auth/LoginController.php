<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\{User};

use App\Http\Controllers\Controller;
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

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
            ->scopes([])
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

        try {
            $discordMember = $discord->guild->getGuildMember(['guild.id' => (int)env('GUILD_ID'), 'user.id' => (int)$id]);
        } catch (\GuzzleHttp\Command\Exception\CommandClientException $e) {
            abort(404, "Doesn't look like you're in the guild Discord server.");
        }

        if (!$discordMember) {
            abort(404, "Doesn't look like you're in the guild Discord server.");
        }


        $authUser = $this->findUser($unauthUser, 'discord');

        if ($authUser) {
            if ($authUser->banned_at) {
                abort(403, 'You have been banned.');
            }
            Auth::login($authUser, true);
            return redirect($this->redirectTo);
        } else if ($unauthUser) {
            $user = User::create([
                'username'         => $unauthUser->getName(),
                'email'            => $unauthUser->getEmail(),
                'discord_username' => $unauthUser->getNickname(),
                'discord_id'       => $id,
                'discord_avatar'   => $unauthUser->getAvatar(),
                'password'         => null,
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
        if (!$authUser) {
            $userWithSameEmail = User::where('email', $user->email)->first();
            if ($userWithSameEmail) {
                if (!$userWithSameEmail->$serviceField) {
                // This user already exists but they haven't linked this social platform yet.
                // Link their account to this social platform and log them in.
                    $userWithSameEmail->$serviceField = $user->id;
                    $userWithSameEmail->save();
                    $authUser = $userWithSameEmail;
                } else {
                // Email already taken and registered with a different service account
                    abort(403, 'Cannot proceed. Someone already registered ' . $user->email . ' and linked it to a different ' . $service . ' account. (' . $user->email . ' is the email ' . $service . ' just gave us) Sorry! Try using a different account.');
                }
            }
        }
        return $authUser;
    }
}
