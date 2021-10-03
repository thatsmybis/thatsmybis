<?php
namespace App\Http\Controllers\Api;

use App\Item;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ItemController extends \App\Http\Controllers\Controller
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
            'expansion_id' => 'integer|min:1|max:99',
            'query'        => 'string|min:1|max:40',
            'locale'       => ['nullable', 'string', Rule::in([
                    "de",
                    "en",
                    "es",
                    "fr",
                    "it",
                    "pt",
                    "ru",
                    "ko",
                    "cn",
                ])
            ],
        ];
    }

    /**
     * Lookup items similar to a given query.
     *
     * @param $query The name to search for.
     * @return \Illuminate\Http\Response
     */
    public function query($expansionId, $query)
    {
        $locale = request()->input('locale');

        $validator = Validator::make([
            'expansion_id' => $expansionId,
            'query'        => $query,
            'locale'       => $locale,
        ], $this->getValidationRules());

        if ($validator->fails()) {
            return response()->json([
                    'error' => __('Query must be between 1 and :charLimit characters. Expansion ID must be between 1 and :expansionLimit.', ['charLimit' => 40, 'expansionLimit' => 3])
                ],
                403);
        } else {
            if ($query && $query != " ") {
                $sqlQuery = Item::orderByDesc('weight')
                    ->limit(15);

                if ($locale) {
                    $sqlQuery = $sqlQuery->select(['name', "name_{$locale}", 'item_id', 'quality'])
                    ->where([
                        ["name_{$locale}", 'like', '%' . trim($query) . '%'],
                        ['expansion_id', $expansionId],
                    ])
                    ->orWhere([
                        ['name', 'like', '%' . trim($query) . '%'],
                        ['expansion_id', $expansionId],
                    ])
                    ->orderBy("name_{$locale}")
                    ->orderBy('name');
                } else {
                    $sqlQuery = $sqlQuery->select(['name', 'item_id'])
                    ->where([
                        ['name', 'like', '%' . trim($query) . '%'],
                        ['expansion_id', $expansionId],
                    ])
                    ->orderBy('name');
                    // For a more performant/powerful query...
                    // ->whereRaw(
                    //     "MATCH(`items`.`name`) AGAINST(? IN BOOLEAN MODE)",
                    //     ['+' . $query . ''] // note the prefixed or suffixed character(s) https://dev.mysql.com/doc/refman/5.5/en/fulltext-boolean.html
                    // )
                }

                $results = $sqlQuery->get();

                // For testing the query time:
                // $start = microtime(true);
                // $results = $results->get();
                // $end = microtime(true);
                // Log::debug($query . " (FULLTEXT): " . round(($end - $start) * 1000, 3) . "ms");

                // We just want the names in a plain old array; not key:value.
                $results = $results->transform(function ($item) use ($locale) {
                    if ($locale) {
                        return ['value' => $item['item_id'], 'label' => ($item["name_{$locale}"] ? $item["name_{$locale}"] : $item['name'])];
                    } else {
                        return ['value' => $item['item_id'], 'label' => $item['name']];
                    }
                });
            } else {
                return response()->json([], 200);
            }

            return response()->json($results, 200);
        }
    }
}
