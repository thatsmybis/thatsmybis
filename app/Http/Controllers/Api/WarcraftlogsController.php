<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\WarcraftlogsController as AuthWarcraftlogsController;
use App\Http\Controllers\RaidController;
use App\{Guild, Member};
use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WarcraftlogsController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([
            // We need access to the session.
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
    }

    /**
     * Lookup items similar to a given query.
     *
     * @param $query The name to search for.
     * @return \Illuminate\Http\Response
     */
    public function attendees()
    {
        $validator = Validator::make(
            [
                'guild_id' => request()->input('guild_id'),
                'codes'    => request()->input('codes'),
            ],
            [
                'guild_id' => 'integer|exists:guilds,id',
                'codes.*'  => 'nullable|string|min:1|max:30',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                    'error' => __('Guild ID and an array of Warcraft Logs report codes are required.')
                ],
                403);
        }

        $member = Member::where(['guild_id' => request()->input('guild_id'), 'user_id' => Auth::id()])->first();

        if (!$member) {
            return response()->json([
                'error' => __('You are not a member of that guild.')
            ],
            403);
        }

        $guild = Guild::find(request()->input('guild_id'));

        if (!request()->input('codes') || !count(request()->input('codes'))) {
            return response()->json([], 200);
        }

        $reports = $this->getReportsWithAttendees(request()->input('codes'), $guild);

        return response()->json($reports, 200);
    }

    /**
     * Fetch Warcraft Logs reports for the given codes.
     * Reports will include some metadata and the characters in attendance.
     *
     * @param array[string] $codes Warcraft Logs report codes
     * @param Guild $guild
     *
     * @return array A list of the reports that were successfully fetched.
     */
    private function getReportsWithAttendees($codes, $guild) {
        AuthWarcraftlogsController::renewTokenIfNeeded($guild);

        $reports = [];

        $client = new Client;

        $codes = array_slice($codes, 0, RaidController::MAX_LOGS);

        foreach ($codes as $code) {
            $graphQl = [
                'query' => 'query ($code: String) { reportData { report(code: $code) { code endTime startTime title zone { id name } rankedCharacters { classID name } } } }',
                'variables' => ['code' => $code],
            ];

            $response = $client->post(
                "https://www.warcraftlogs.com/api/v2/user",
                [
                    "headers" => [
                        "Authorization" => "Bearer " . $guild->warcraftlogs_token,
                        "Content-Type"  => "application/json",
                    ],
                    "body" => json_encode($graphQl)
                ]
            );

            $result = json_decode((string) $response->getBody(), true);

            if (count($result['data'])) {
                $reports[$code] = $result['data']['reportData']['report'];
            } else {
                $reports[$code] = null;
            }
        }

        return $reports;
    }
}
