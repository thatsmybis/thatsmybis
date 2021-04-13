<?php

namespace App\Http\Controllers\Api;

use App\Raid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Log;

class RaidController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    private function getValidationRules() {
        return [
            'guild_id' => 'integer|exists:guilds',
            'query'    => 'string|min:1|max:40',
        ];
    }

    /**
     * Lookup guild raids similar to a given query.
     *
     * @param $query The name to search for.
     * @return \Illuminate\Http\Response
     */
    public function query($guildID, $query)
    {
        // TODO: Copied from itemscontroller
        $validator = Validator::make([
            'guild_id' => $guildID,
            'query'    => $query
        ], $this->getValidationRules());

        if ($validator->fails()) {
            return response()->json(['error' => 'Query did not pass validation. Query must be between 1 and 40 characters. Guild ID must match your guild.'], 403);
        } else {
            if ($query && $query != " ") {
                $results = Raid::select(['name', 'date', 'id'])
                    ->where([
                        ['guild_id', $guildID],
                        ['name', 'like', '%' . trim($query) . '%'],
                    ])
                    // For a more performant/powerful query...
                    // ->whereRaw(
                    //     "MATCH(`raids`.`name`) AGAINST(? IN BOOLEAN MODE)",
                    //     ['+' . $query . ''] // note the prefixed or suffixed character(s) https://dev.mysql.com/doc/refman/5.5/en/fulltext-boolean.html
                    // )
                    ->orderByDesc('date')
                    ->limit(15)
                    ->get();

                // For testing the query time:
                // $start = microtime(true);
                // $results = $results->get();
                // $end = microtime(true);
                // Log::debug($query . " (FULLTEXT): " . round(($end - $start) * 1000, 3) . "ms");

                // We just want the names in a plain old array; not key:value.
                $results = $results->transform(function ($raid) {
                    return ['value' => $raid['id'], 'label' => $raid['name']]; // TODO: add in raid name to label?
                });
            } else {
                return response()->json([], 200);
            }

            return response()->json($results, 200);
        }
    }
}
