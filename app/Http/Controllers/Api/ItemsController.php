<?php

namespace App\Http\Controllers\Api;

use App\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ItemsController extends \App\Http\Controllers\Controller
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
            'query' => 'string|min:1|max:40'
        ];
    }

    /**
     * Lookup items similar to a given query.
     *
     * @param $query The name to search for.
     * @return \Illuminate\Http\Response
     */
    public function query($query)
    {
        $validator = Validator::make(['query' => $query], $this->getValidationRules());

        if ($validator->fails()) {
            return response()->json(['error' => 'Query did not pass validation. Query must be between 1 and 40 characters.'], 403);
        } else {
            if ($query && $query != " ") {
                $results = Item::select(['id', 'name', 'item_id'])
                    ->whereRaw(
                        "MATCH(`items`.`name`) AGAINST(? IN BOOLEAN MODE)",
                        [$query . '*'] // note the wildcard added to the end
                    )
                    ->limit(10)
                    ->get();

                // For testing the query time:
                // $start = microtime(true);
                // $results = $results->get();
                // $end = microtime(true);
                // logThis($query . " (FULLTEXT): " . round(($end - $start) * 1000, 3) . "ms");

                // We just want the names in a plain old array; not key:value.
                $results = $results->transform(function ($item) {
                    return ['value' => $item['id'], 'label' => $item['name']];
                });
            } else {
                return response()->json([], 200);
            }

            return response()->json($results, 200);
        }
    }
}
