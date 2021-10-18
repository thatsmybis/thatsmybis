<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\{AuditLog, Guild, Member, User};
use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;

class WarcraftlogsController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    /**
     * Redirect the user to the Warcraft Logs authentication page.
     *
     * @return Response
     */
    public function redirectToWarcraftlogs($guildId, $guildSlug) {
        $guild         = request()->get('guild');
        $currentMember = request()->get('currentMember');

        // Store the current Guild ID so that we know which guild to associate the
        // warcraftlogs account with after warcraftlogs redirects back to our app.
        request()->session()->put('warcraftlogsGuildId', $guild->id);

        return Socialite::driver('warcraftlogs')->redirect();
    }

    /**
     * Obtain the auth tokens for a user from Warcraft Logs
     *
     * @return Response
     */
    public function handleWarcraftlogsCallback()
    {
        $result = null;
        $warcraftlogsUser = null;
        try {
            // driver is defined in App\Providers\Warcraftlogs
            $result = Socialite::driver('warcraftlogs');
            $warcraftlogsUser = Socialite::driver('warcraftlogs')->user();
        } catch (Exception $e) {
            return redirect('auth/warcraftlogs');
        }

        if (!$warcraftlogsUser->token) {
            abort(403, __("Didn't receive an API token from Warcraft Logs. Try again."));
        }

        $guildId = request()->session()->pull('warcraftlogsGuildId');

        if (!$guildId) {
            abort(403, __("Guild ID missing. Try again."));
        }

        $guild = Guild::find($guildId);

        $user = request()->get('currentUser');
        $member = Member::
            where(['guild_id' => $guild->id, 'user_id' => $user->id])
            ->whereNull('banned_at')
            ->whereNull('inactive_at')
            ->first();

        if (!$member) {
            abort(403, __("Unable to find an active membership for your account in that guild."));
        }

        if ($guild) {
            $guild->update([
                'warcraftlogs_token'         => $warcraftlogsUser->token,
                'warcraftlogs_refresh_token' => $warcraftlogsUser->refreshToken,
                'warcraftlogs_token_expiry'  => date('Y-m-d H:i:s', time() + $warcraftlogsUser->expiresIn),
                'warcraftlogs_member_id'     => $member->id,
            ]);

            AuditLog::create([
                'description' => $member->username . ' connected the guild to Warcraft Logs',
                'member_id'   => $member->id,
                'guild_id'    => $guild->id,
            ]);

            request()->session()->flash('status', __("Warcraft Logs linked to guild. Revisit your guild settings to input your guild's ID."));

            // Redirect with fragment
            return redirect(route('guild.settings', ['guildId' => $guild->id, 'guildSlug' => $guild->slug]) . "#warcraftlogs");
        } else {
            abort(403, __("Something went wrong with the data Warcraft Logs sent us. Try again."));
        }
    }

    /**
     * Check the token's expiry... if the token is expired, renew it.
     *
     * @param Guild $guild
     */
    public static function renewTokenIfNeeded($guild) {
        if ($guild->warcraftlogs_token_expiry && $guild->warcraftlogs_token_expiry < getDateTime()) {
            if ($guild->warcraftlogs_refresh_token) {
                try {
                    $client = new Client;
                    $response = $client->post('https://www.warcraftlogs.com/oauth/token', [
                        'form_params' => [
                            'client_id'     => env('WARCRAFTLOGS_CLIENT_ID'),
                            'client_secret' => env('WARCRAFTLOGS_CLIENT_SECRET'),
                            'grant_type'    => 'refresh_token',
                            'refresh_token' => $guild->warcraftlogs_refresh_token,
                            'redirect_uri'  => env('WARCRAFTLOGS_REDIRECT_URI'),
                        ],
                    ]);

                    $result = json_decode((string) $response->getBody(), true);

                    $guild->update([
                        'warcraftlogs_token'         => $result['access_token'],
                        'warcraftlogs_refresh_token' => $result['refresh_token'],
                        'warcraftlogs_token_expiry'  => date('Y-m-d H:i:s', time() + $result['expires_in']),
                    ]);
                } catch (ClientException $e) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}
