<?php

namespace App\Http\Middleware;

use Auth, Closure;
use App\Role;
use App\Notification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\App;

class SeeUser
{
    /**
     * Handle an incoming request.
     *
     * Stores the user object in the request for convenient access later on.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            // You shall not pass!
            if ($user->banned_at) {
                Auth::guard()->logout();
                $request->session()->invalidate();
                abort(403, 'You have been banned.');
            }

            // Try to refresh token without requiring user to sign in again
            if ($user->discord_token_expiry && $user->discord_token_expiry < getDateTime()) {
                if ($user->discord_refresh_token) {
                    try {
                        $http = new Client;

                        $response = $http->post('https://discord.com/api/oauth2/token', [
                            'form_params' => [
                                'client_id'     => env('DISCORD_KEY'),
                                'client_secret' => env('DISCORD_SECRET'),
                                'grant_type'    => 'refresh_token',
                                'refresh_token' => $user->discord_refresh_token,
                                'redirect_uri'  => env('DISCORD_REDIRECT_URI'),
                                'scope'         => 'identify guilds',
                            ],
                        ]);

                        $result = json_decode((string) $response->getBody(), true);

                        $user->update([
                                'discord_token'         => $result['access_token'],
                                'discord_refresh_token' => $result['refresh_token'],
                                'discord_token_expiry'  => date('Y-m-d H:i:s', time() + $result['expires_in']),
                            ]);
                    } catch (ClientException $e) {
                        Auth::logout();
                        return redirect('discordLogin');
                    }
                } else {
                    Auth::logout();
                    return redirect('discordLogin');
                }
            }

            // Did we get the param to bust the cache?
            $bustCache = false;
            if (!empty(request()->input('b')) && request()->input('b')) {
                $bustCache = true;
            }

            // For translations
            App::setLocale($user->locale);

            // Store the user for later access.
            $request->attributes->add([
                'bustCache'      => $bustCache,
                'currentUser'    => $user,
                'isAdmin'        => $user->is_admin,
                'isStreamerMode' => $user->is_streamer_mode
            ]);
        }

        return $next($request);
    }
}
